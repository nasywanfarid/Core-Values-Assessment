<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $users = \App\Models\User::where('role', 'karyawan')->get();
        $indicators = \App\Models\Indicator::all();
        
        $totalKaryawan = \App\Models\User::where('role', 'karyawan')->count();
        $totalDivisi = \App\Models\Division::count();
        $completedAssignments = \App\Models\ReviewerAssignment::where('status', 'completed')->count();
        $pendingAssignments = \App\Models\ReviewerAssignment::where('status', 'pending')->count();

        // --- Ringkasan Dashboard ---
        
        // 1. Ambil periode terbaru yang memiliki penilaian (berdasarkan tanggal penilaian terbaru)
        $latestPeriod = \App\Models\ReviewerAssignment::orderBy('assessment_date', 'desc')->first();

        $gradeSummary = [
            'A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'E' => 0
        ];
        $clusterSummary = [
            'Implementasi Core Values Tinggi' => 0, 'Implementasi Core Values Rendah' => 0
        ];
        $latestPeriodDate = null;
        $employeeResults = [];

        if ($latestPeriod) {
            $latestPeriodDate = $latestPeriod->assessment_date;
            
            // Ambil semua karyawan yang memiliki penugasan pada periode tersebut
            $employees = \App\Models\User::whereHas('assignmentsAsReviewee', function($q) use ($latestPeriodDate) {
                $q->where('assessment_date', $latestPeriodDate);
            })->with(['assignmentsAsReviewee' => function($q) use ($latestPeriodDate) {
                $q->where('assessment_date', $latestPeriodDate)->with('assessments');
            }])->get();

            $dataForClustering = [];
            $indicatorMap = [
                1 => 'Bahagia', 2 => 'Etika', 3 => 'Responsif', 4 => 'Hangat',
                5 => 'Amanah', 6 => 'Semangat', 7 => 'Inovatif', 8 => 'Loyal'
            ];

            foreach ($employees as $employee) {
                $assignments = $employee->assignmentsAsReviewee;
                $totalSum = 0;
                $count = $assignments->count();

                if ($count > 0) {
                    // Data untuk clustering
                    $row = ['id' => $employee->id, 'name' => $employee->name];
                    foreach ($indicatorMap as $name) { $row[$name] = 0; }

                    foreach ($indicators as $indicator) {
                        $indSum = 0;
                        foreach ($assignments as $as) {
                            $score = $as->assessments->where('indicator_id', $indicator->id)->first();
                            if ($score) $indSum += $score->score;
                        }
                        $feat = $indicatorMap[$indicator->id] ?? null;
                        if ($feat) $row[$feat] = $indSum / $count;
                    }

                    foreach ($assignments as $as) { $totalSum += $as->assessments->sum('score'); }
                    $avgScore = $totalSum / $count;
                    
                    // Hitung Grade
                    $grade = $this->calculateGrade($avgScore)['grade'];
                    if (isset($gradeSummary[$grade])) $gradeSummary[$grade]++;

                    $row['total_score'] = $avgScore;
                    $dataForClustering[] = $row;
                }
            }

            // Panggil Flask untuk Clustering
            $employeeResults = [];
            if (!empty($dataForClustering)) {
                $clustered = $this->getClustersFromPython($dataForClustering);
                
                // Gabungkan data clustering kembali ke daftar karyawan untuk ditampilkan di tabel
                foreach ($clustered as $item) {
                    $kat = $item['Kategori'] ?? '-';
                    if (isset($clusterSummary[$kat])) $clusterSummary[$kat]++;
                    
                    // Cari grade yang sudah dihitung sebelumnya
                    $empGrade = 'E';
                    foreach ($dataForClustering as $dfc) {
                        if ($dfc['id'] == $item['id']) {
                            $empGrade = $this->calculateGrade($dfc['total_score'])['grade'];
                            break;
                        }
                    }

                    $employeeResults[] = (object) [
                        'name' => $item['name'],
                        'grade' => $empGrade,
                        'kategori' => $kat,
                        'score' => round($item['total_score'], 2)
                    ];
                }
            }
        }

        return view('admin.dashboard', compact(
            'totalKaryawan', 
            'totalDivisi', 
            'completedAssignments', 
            'pendingAssignments',
            'gradeSummary',
            'clusterSummary',
            'latestPeriodDate',
            'employeeResults'
        ));
    }

    private function calculateGrade($score)
    {
        if ($score >= 37) return ['grade' => 'A'];
        if ($score >= 30) return ['grade' => 'B'];
        if ($score >= 20) return ['grade' => 'C'];
        if ($score >= 10) return ['grade' => 'D'];
        return ['grade' => 'E'];
    }

    private function getClustersFromPython($data)
    {
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(3)->post('http://localhost:5000/cluster', $data);
            if ($response->successful()) return $response->json();
        } catch (\Exception $e) {}
        return array_map(function($i) { $i['Kategori'] = '-'; return $i; }, $data);
    }
}
