<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KaryawanController extends Controller
{
    public function index()
    {
        $pendingAssignments = \App\Models\ReviewerAssignment::with('reviewee')
            ->where('reviewer_id', auth()->id())
            ->where('status', 'pending')
            ->where('is_active', true)
            ->get();
            
        $completedAssignments = \App\Models\ReviewerAssignment::with('reviewee')
            ->where('reviewer_id', auth()->id())
            ->where('status', 'completed')
            ->get();

        return view('karyawan.dashboard', compact('pendingAssignments', 'completedAssignments'));
    }

    public function evaluate(\App\Models\ReviewerAssignment $assignment)
    {
        if ($assignment->reviewer_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk penilaian ini.');
        }

        if (!$assignment->is_active) {
            abort(403, 'Sesi penilaian ini belum diaktifkan oleh Admin/HR.');
        }

        if ($assignment->status !== 'pending') {
            abort(403, 'Penilaian ini sudah selesai dilakukan.');
        }

        $indicators = \App\Models\Indicator::all();
        return view('karyawan.evaluate', compact('assignment', 'indicators'));
    }

    public function storeEvaluation(Request $request, \App\Models\ReviewerAssignment $assignment)
    {
        if ($assignment->reviewer_id !== auth()->id() || !$assignment->is_active || $assignment->status !== 'pending') {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'scores' => 'required|array',
            'scores.*' => 'required|integer|min:1|max:5',
            'reasons' => 'required|array',
            'reasons.*' => 'required|string|min:5',
        ]);

        foreach ($request->scores as $indicatorId => $score) {
            \App\Models\Assessment::create([
                'assignment_id' => $assignment->id,
                'indicator_id' => $indicatorId,
                'score' => $score,
                'reason' => $request->reasons[$indicatorId] ?? null,
            ]);
        }

        $assignment->update(['status' => 'completed']);

        return redirect()->route('karyawan.dashboard')->with('success', 'Penilaian berhasil disimpan. Terima kasih atas partisipasi Anda.');
    }
}