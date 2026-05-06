<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Division;
use App\Models\Indicator;
use App\Models\ReviewerAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        // Hapus semua penugasan (dan otomatis asesmennya karena cascade) pada tanggal & cabang tersebut
        ReviewerAssignment::where('assessment_date', $request->date)
            ->whereHas('reviewer', function($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            })
            ->delete();

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
