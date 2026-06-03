<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Division;
use App\Models\Indicator;
use App\Models\ReviewerAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class AssessmentResultController extends Controller
{
    public function index(Request $request)
    {
        $branches = Branch::all();
        
        // Query dasar
        $query = ReviewerAssignment::select('assessment_date', 'users.branch_id', 'branches.name as branch_name')
            ->join('users', 'reviewer_assignments.reviewer_id', '=', 'users.id')
            ->join('branches', 'users.branch_id', '=', 'branches.id');

        // Filter Cabang
        if ($request->filled('branch_id')) {
            $query->where('users.branch_id', $request->branch_id);
        }

        $periods = $query->groupBy('assessment_date', 'users.branch_id', 'branches.name')
            ->orderBy('assessment_date', 'desc')
            ->get();

        // Hitung jumlah karyawan yang dinilai per periode
        foreach ($periods as $period) {
            $period->employee_count = ReviewerAssignment::where('assessment_date', $period->assessment_date)
                ->whereHas('reviewer', function($q) use ($period) {
                    $q->where('branch_id', $period->branch_id);
                })
                ->distinct('reviewee_id')
                ->count('reviewee_id');
        }

        return view('admin.results.index', compact('periods', 'branches'));
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'branch_id' => 'required|exists:branches,id'
        ]);

        $normalizedDate = \Carbon\Carbon::parse($request->date)->startOfMonth()->format('Y-m-d');

        // Cari semua ID penugasan pada periode & cabang tersebut
        $assignmentIds = ReviewerAssignment::where('assessment_date', $normalizedDate)
            ->whereHas('reviewer', function($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            })
            ->pluck('id');

        // Hapus penugasan (otomatis hapus asesmen karena cascade di database)
        ReviewerAssignment::whereIn('id', $assignmentIds)->delete();

        return redirect()->back()->with('success', 'Data penilaian periode tersebut berhasil dihapus.');
    }

    public function showEmployees(Request $request, $date, $branchId)
    {
        $branch = Branch::findOrFail($branchId);
        $divisions = Division::all();

        // Ambil semua karyawan yang menjadi subjek penilaian di periode & cabang ini
        $query = User::whereHas('assignmentsAsReviewee', function($q) use ($date, $branchId) {
            $q->where('assessment_date', $date)
              ->whereHas('reviewer', function($rq) use ($branchId) {
                  $rq->where('branch_id', $branchId);
              });
        })->with(['division', 'assignmentsAsReviewee' => function($q) use ($date) {
            $q->where('assessment_date', $date)->with('assessments');
        }]);

        // Filter Divisi
        if ($request->filled('division_id')) {
            $query->where('division_id', $request->division_id);
        }

        // Pencarian Nama
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $employees = $query->get();

        // Hitung nilai dan grade untuk setiap karyawan
        $results = $employees->map(function($user) {
            $assignments = $user->assignmentsAsReviewee;
            $totalSum = 0;
            $reviewerCount = $assignments->count();

            foreach ($assignments as $assignment) {
                $totalSum += $assignment->assessments->sum('score');
            }

            $finalScore = $reviewerCount > 0 ? $totalSum / $reviewerCount : 0;
            $gradeInfo = $this->calculateGrade($finalScore);

            return (object) [
                'id' => $user->id,
                'name' => $user->name,
                'division' => $user->division->name ?? '-',
                'total_score' => round($finalScore, 2),
                'grade' => $gradeInfo['grade'],
                'description' => $gradeInfo['description']
            ];
        });

        // Urutkan berdasarkan nilai tertinggi
        $results = $results->sortByDesc('total_score');

        return view('admin.results.period', compact('results', 'date', 'branch', 'divisions'));
    }

    private function getClustersFromPython($data)
    {
        if (empty($data)) return $data;

        try {
            // Timeout 5 detik agar tidak menghambat loading jika API mati
            $response = Http::timeout(5)->post('http://localhost:5000/cluster', $data);
            
            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            // Log error jika perlu, tapi biarkan aplikasi tetap jalan
            \Log::warning("Gagal terhubung ke Flask API: " . $e->getMessage());
        }

        // Jika gagal, tambahkan kolom Kategori kosong agar tidak error di view
        return array_map(function($item) {
            $item['Kategori'] = '-';
            return $item;
        }, $data);
    }

    public function exportExcel($date, $branchId)
    {
        $branch = Branch::findOrFail($branchId);
        $fileName = 'Hasil_Penilaian_' . $branch->name . '_' . $date . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\AssessmentResultsExport($date, $branchId, [$this, 'calculateGrade']),
            $fileName
        );
    }

    public function showDetail($date, $branchId, $userId)
    {
        $employee = User::findOrFail($userId);
        $indicators = Indicator::all();
        
        // Ambil semua penugasan (reviewer) untuk karyawan ini di periode tersebut
        $assignments = ReviewerAssignment::where('reviewee_id', $userId)
            ->where('assessment_date', $date)
            ->with(['reviewer', 'assessments.indicator'])
            ->get();

        // Hitung Rata-rata dan Total per Indikator
        $indicatorAverages = [];
        $indicatorTotals = [];
        foreach ($indicators as $indicator) {
            $sum = 0;
            $count = 0;
            foreach ($assignments as $assignment) {
                $score = $assignment->assessments->where('indicator_id', $indicator->id)->first();
                if ($score) {
                    $sum += $score->score;
                    $count++;
                }
            }
            $indicatorTotals[$indicator->id] = $sum;
            $indicatorAverages[$indicator->id] = $count > 0 ? $sum / $count : 0;
        }

        // Hitung Total Akhir
        $totalSum = 0;
        foreach ($assignments as $assignment) {
            $totalSum += $assignment->assessments->sum('score');
        }
        $finalAverage = $assignments->count() > 0 ? $totalSum / $assignments->count() : 0;
        $gradeInfo = $this->calculateGrade($finalAverage);

        return view('admin.results.detail', compact(
            'employee', 
            'assignments', 
            'indicators', 
            'indicatorAverages', 
            'indicatorTotals',
            'finalAverage', 
            'gradeInfo',
            'date'
        ));
    }

    public function clustering(Request $request)
    {
        $branches = Branch::all();
        
        // Get available periods for the dropdown
        $periods = ReviewerAssignment::select('assessment_date')
            ->groupBy('assessment_date')
            ->orderBy('assessment_date', 'desc')
            ->get();

        $selectedDate = $request->date;
        $selectedBranchId = $request->branch_id;
        $results = null;
        $summary = null;
        $branch = null;

        if ($selectedDate && $selectedBranchId) {
            $branch = Branch::find($selectedBranchId);
            
            // Ambil semua karyawan yang menjadi subjek penilaian di periode & cabang ini
            $employees = User::whereHas('assignmentsAsReviewee', function($q) use ($selectedDate, $selectedBranchId) {
                $q->where('assessment_date', $selectedDate)
                  ->whereHas('reviewer', function($rq) use ($selectedBranchId) {
                      $rq->where('branch_id', $selectedBranchId);
                  });
            })->with(['division', 'assignmentsAsReviewee' => function($q) use ($selectedDate) {
                $q->where('assessment_date', $selectedDate)->with('assessments');
            }])->get();

            $indicators = Indicator::all();
            $indicatorMap = [
                1 => 'Bahagia', 2 => 'Etika', 3 => 'Responsif', 4 => 'Hangat',
                5 => 'Amanah', 6 => 'Semangat', 7 => 'Inovatif', 8 => 'Loyal'
            ];

            $dataForClustering = $employees->map(function($user) use ($indicators, $indicatorMap) {
                $assignments = $user->assignmentsAsReviewee;
                
                // Pastikan ada setidaknya satu tugas penilaian yang sudah diisi (assessments tidak kosong)
                $hasAssessments = $assignments->contains(function($assignment) {
                    return $assignment->assessments->isNotEmpty();
                });

                if (!$hasAssessments) {
                    return null;
                }

                $totalSum = 0;
                $reviewerCount = $assignments->count();

                $row = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'division' => $user->division->name ?? '-',
                ];

                foreach ($indicatorMap as $name) { $row[$name] = 0; }

                if ($reviewerCount > 0) {
                    foreach ($indicators as $indicator) {
                        $indicatorSum = 0;
                        foreach ($assignments as $assignment) {
                            $assessment = $assignment->assessments->where('indicator_id', $indicator->id)->first();
                            if ($assessment) $indicatorSum += $assessment->score;
                        }
                        $featureName = $indicatorMap[$indicator->id] ?? null;
                        if ($featureName) $row[$featureName] = $indicatorSum / $reviewerCount;
                    }
                }

                foreach ($assignments as $assignment) { $totalSum += $assignment->assessments->sum('score'); }
                $row['total_score'] = round($reviewerCount > 0 ? $totalSum / $reviewerCount : 0, 2);
                return $row;
            })->filter()->values()->toArray();

            // Panggil Flask API
            $clusteredData = $this->getClustersFromPython($dataForClustering);
            
            // Group by Kategori
            $results = collect($clusteredData)->map(function($item) {
                return (object) $item;
            })->groupBy('Kategori');

            // Hitung summary
            $summary = [
                'Implementasi Core Values Tinggi' => isset($results['Implementasi Core Values Tinggi']) ? $results['Implementasi Core Values Tinggi']->count() : 0,
                'Implementasi Core Values Rendah' => isset($results['Implementasi Core Values Rendah']) ? $results['Implementasi Core Values Rendah']->count() : 0,
            ];
        }

        return view('admin.results.clustering', compact('branches', 'periods', 'results', 'summary', 'selectedDate', 'selectedBranchId', 'branch'));
    }

    public function calculateGrade($score)
    {
        if ($score >= 37) {
            return [
                'grade' => 'A',
                'description' => 'Mewakili Nilai Perusahaan Secara Konsisten dan Luar Biasa. Individu menunjukkan sikap, perilaku, dan kinerja yang mencerminkan core value perusahaan dalam segala situasi.'
            ];
        } elseif ($score >= 30) {
            return [
                'grade' => 'B',
                'description' => 'Mewakili Nilai Perusahaan dengan Baik. Umumnya menunjukkan perilaku yang selaras dengan core value, dengan perbaikan kecil yang mungkin diperlukan.'
            ];
        } elseif ($score >= 20) {
            return [
                'grade' => 'C',
                'description' => 'Cukup Mencerminkan Nilai Perusahaan. Masih ada beberapa aspek perilaku yang perlu ditingkatkan untuk menyelaraskan diri sepenuhnya dengan core value.'
            ];
        } elseif ($score >= 10) {
            return [
                'grade' => 'D',
                'description' => 'Kurang Mencerminkan Nilai Perusahaan. Sering menunjukkan perilaku yang tidak sesuai dengan core value. Perlu perhatian dan pembinaan intensif.'
            ];
        } else {
            return [
                'grade' => 'E',
                'description' => 'Tidak Mencerminkan Nilai Perusahaan. Tidak menunjukkan perilaku yang sejalan dengan core value perusahaan. Dibutuhkan tindakan korektif dan pendampingan.'
            ];
        }
    }
}
