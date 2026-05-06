<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InteractionMatrixController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $branches = \App\Models\Branch::all();
        $selectedBranchId = $request->branch_id;
        $selectedDate = $request->date;
        $employees = [];
        $existingAssignments = [];

        if ($selectedBranchId) {
            $employees = \App\Models\User::where('branch_id', $selectedBranchId)
                ->whereIn('role', ['karyawan', 'direktur'])
                ->get();
            
            if ($selectedDate) {
                $existingAssignments = \App\Models\ReviewerAssignment::where('assessment_date', $selectedDate)
                    ->get()
                    ->groupBy('reviewer_id')
                    ->map(function ($group) {
                        return $group->pluck('reviewee_id')->toArray();
                    })
                    ->toArray();
            }
        }

        $history = \App\Models\ReviewerAssignment::selectRaw('assessment_date, branches.name as branch_name, branches.id as branch_id, count(*) as total_assignments, sum(case when reviewer_assignments.status="completed" then 1 else 0 end) as completed_assignments')
            ->join('users', 'reviewer_assignments.reviewer_id', '=', 'users.id')
            ->join('branches', 'users.branch_id', '=', 'branches.id')
            ->groupBy('assessment_date', 'branches.id', 'branches.name')
            ->orderBy('assessment_date', 'desc')
            ->paginate(10);

        $assignmentsCount = \App\Models\ReviewerAssignment::count();
        return view('admin.interaction-matrices.index', compact('branches', 'employees', 'selectedBranchId', 'selectedDate', 'existingAssignments', 'assignmentsCount', 'history'));
    }

    public function storeAssignments(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'assessment_date' => 'required|date',
        ]);

        $branchId = $request->branch_id;
        $date = $request->assessment_date;
        $matrix = $request->input('matrix', []); // [reviewer_id => [reviewee_id1, reviewee_id2, ...]]

        // Ambil semua reviewer_id yang ada di cabang ini untuk tanggal tersebut
        $allReviewerIds = \App\Models\User::where('branch_id', $branchId)
            ->whereIn('role', ['karyawan', 'direktur'])
            ->pluck('id')
            ->toArray();

        // Bangun daftar pasangan yang DICEKLIS dari form
        $checkedPairs = [];
        foreach ($matrix as $reviewerId => $revieweeIds) {
            foreach ($revieweeIds as $revieweeId) {
                if ($reviewerId != $revieweeId) {
                    $checkedPairs[] = ['reviewer_id' => (int)$reviewerId, 'reviewee_id' => (int)$revieweeId];
                }
            }
        }

        // Hapus assignment PENDING yang reviewer-nya ada di cabang ini
        // dan pasangan (reviewer, reviewee)-nya TIDAK ADA di checkedPairs
        $existingPendings = \App\Models\ReviewerAssignment::where('assessment_date', $date)
            ->whereIn('reviewer_id', $allReviewerIds)
            ->where('status', 'pending')
            ->get();

        foreach ($existingPendings as $pending) {
            $stillChecked = false;
            foreach ($checkedPairs as $pair) {
                if ($pair['reviewer_id'] === $pending->reviewer_id && $pair['reviewee_id'] === $pending->reviewee_id) {
                    $stillChecked = true;
                    break;
                }
            }
            if (!$stillChecked) {
                $pending->delete();
            }
        }

        // Buat atau pertahankan assignment yang diceklis
        $newCount = 0;
        foreach ($checkedPairs as $pair) {
            \App\Models\ReviewerAssignment::updateOrCreate(
                [
                    'reviewer_id'     => $pair['reviewer_id'],
                    'reviewee_id'     => $pair['reviewee_id'],
                    'assessment_date' => $date,
                ],
                [
                    'status' => 'pending',
                ]
            );
            $newCount++;
        }

        return redirect()->route('admin.interaction-matrices.index', ['branch_id' => $branchId, 'date' => $date])
            ->with('success', "Matriks penugasan berhasil diperbarui. Total {$newCount} penugasan aktif.");
    }

    public function destroyAssignment(\App\Models\ReviewerAssignment $assignment)
    {
        if (auth()->user()->role === 'hr') abort(403, 'Unauthorized action.');
        $assignment->delete();
        return redirect()->back()->with('success', 'Penugasan dihapus');
    }

    public function bulkDestroy(Request $request)
    {
        if (auth()->user()->role === 'hr') abort(403, 'Unauthorized action.');

        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'date' => 'required|date',
        ]);

        \App\Models\ReviewerAssignment::join('users', 'reviewer_assignments.reviewer_id', '=', 'users.id')
            ->where('users.branch_id', $request->branch_id)
            ->where('assessment_date', $request->date)
            ->delete();

        return redirect()->route('admin.interaction-matrices.index')->with('success', 'Riwayat penugasan untuk cabang dan tanggal tersebut berhasil dihapus.');
    }
}
