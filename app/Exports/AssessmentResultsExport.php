<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Indicator;
use App\Models\Branch;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class AssessmentResultsExport implements FromView, ShouldAutoSize, WithTitle
{
    protected $date;
    protected $branchId;
    protected $calculateGradeCallback;

    public function __construct($date, $branchId, $calculateGradeCallback)
    {
        $this->date = $date;
        $this->branchId = $branchId;
        $this->calculateGradeCallback = $calculateGradeCallback;
    }

    public function title(): string
    {
        return 'Hasil Penilaian ' . $this->date;
    }

    public function view(): View
    {
        $branch = Branch::find($this->branchId);
        $indicators = Indicator::all();
        
        $employees = User::whereHas('assignmentsAsReviewee', function($q) {
            $q->where('assessment_date', $this->date)
              ->whereHas('reviewer', function($rq) {
                  $rq->where('branch_id', $this->branchId);
              });
        })->with(['division', 'assignmentsAsReviewee' => function($q) {
            $q->where('assessment_date', $this->date)->with('assessments');
        }])->get();

        $results = $employees->map(function($user) use ($indicators) {
            $assignments = $user->assignmentsAsReviewee;
            $reviewerCount = $assignments->count();
            
            $indicatorTotals = [];
            $indicatorAverages = [];
            $totalSum = 0;

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

            foreach ($assignments as $assignment) {
                $totalSum += $assignment->assessments->sum('score');
            }

            $finalScore = $reviewerCount > 0 ? $totalSum / $reviewerCount : 0;
            $gradeInfo = call_user_func($this->calculateGradeCallback, $finalScore);

            return (object) [
                'name' => $user->name,
                'division' => $user->division->name ?? '-',
                'assignments' => $assignments,
                'indicator_totals' => $indicatorTotals,
                'indicator_averages' => $indicatorAverages,
                'total_score' => round($finalScore, 2),
                'grade' => $gradeInfo['grade']
            ];
        })->sortByDesc('total_score');

        return view('admin.results.export_excel', [
            'results' => $results,
            'indicators' => $indicators,
            'date' => $this->date,
            'branch' => $branch
        ]);
    }
}
