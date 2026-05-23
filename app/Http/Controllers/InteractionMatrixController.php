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
        $divisions = \App\Models\Division::all();
        $interactions = \App\Models\InteractionMatrix::with(['targetDivision', 'reviewerDivision'])
            ->whereNull('branch_id')
            ->get();

        return view('admin.interaction-matrices.index', compact('divisions', 'interactions'));
    }

    public function storeInteraction(Request $request)
    {
        $request->validate([
            'target_division_id' => 'required|exists:divisions,id',
            'reviewer_division_id' => 'required|exists:divisions,id',
        ]);

        $exists = \App\Models\InteractionMatrix::whereNull('branch_id')
            ->where('target_division_id', $request->target_division_id)
            ->where('reviewer_division_id', $request->reviewer_division_id)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Hubungan divisi ini sudah pernah diinputkan sebelumnya.');
        }

        \App\Models\InteractionMatrix::create([
            'branch_id' => null,
            'target_division_id' => $request->target_division_id,
            'reviewer_division_id' => $request->reviewer_division_id,
        ]);

        return redirect()->back()->with('success', 'Hubungan divisi berhasil ditambahkan secara global.');
    }

    public function destroyInteraction(\App\Models\InteractionMatrix $interaction)
    {
        $interaction->delete();
        return redirect()->back()->with('success', 'Hubungan divisi dihapus.');
    }

    public function bulkDestroyInteractions(Request $request)
    {
        \App\Models\InteractionMatrix::whereNull('branch_id')->delete();
        return redirect()->back()->with('success', 'Semua hubungan divisi global telah dihapus.');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'date' => 'required',
            'count' => 'required|integer|in:3,4,5',
        ]);

        $branchId = $request->branch_id;
        $date = \Carbon\Carbon::parse($request->date)->startOfMonth()->format('Y-m-d');
        $reviewerCountTarget = $request->count;

        // 1. Ambil Relasi Divisi
        $matrix = \App\Models\InteractionMatrix::where('branch_id', $branchId)->get();
        if ($matrix->isEmpty()) {
            return redirect()->back()->with('error', 'Gagal generate: Belum ada relasi divisi yang diatur untuk cabang ini.');
        }

        // 2. Ambil Semua Karyawan Aktif di Cabang Terpilih
        $employees = \App\Models\User::where('branch_id', $branchId)
            ->whereIn('role', ['karyawan', 'direktur'])
            ->get();

        // 3. Ambil History Periode Sebelumnya (untuk rotasi penilai)
        $previousDate = \Carbon\Carbon::parse($date)->subMonth()->format('Y-m-d');
        $history = \App\Models\ReviewerAssignment::where('assessment_date', $previousDate)
            ->get()
            ->groupBy('reviewee_id')
            ->map(function($items) {
                return $items->pluck('reviewer_id')->toArray();
            });

        // 4. Logika Generate
        $assignments = [];
        
        foreach ($employees as $target) {
            // Siapa saja yang bisa menilai target ini?
            // Berdasarkan relasi: cari reviewer_division yang target_division-nya adalah divisi si target
            $allowedReviewerDivIds = $matrix->where('target_division_id', $target->division_id)
                ->pluck('reviewer_division_id')
                ->toArray();
            
            // Calon penilai adalah karyawan di divisi-divisi tersebut
            $candidates = $employees->whereIn('division_id', $allowedReviewerDivIds)
                ->where('id', '!=', $target->id) // Tidak boleh menilai diri sendiri
                ->shuffle();

            // Cek apakah jumlah penilai mencukupi
            if ($candidates->count() < $reviewerCountTarget) {
                return redirect()->back()->with('error', "Gagal generate: Karyawan di divisi penilai untuk {$target->name} ({$target->division->name}) tidak mencukupi (Minimal {$reviewerCountTarget} orang).");
            }

            // Rotasi: Dahulukan yang BELUM menilai di periode sebelumnya
            $prevReviewers = $history->get($target->id, []);
            
            $newReviewers = $candidates->reject(function($c) use ($prevReviewers) {
                return in_array($c->id, $prevReviewers);
            });

            // Jika setelah reject masih cukup, ambil dari yang baru. 
            // Jika tidak cukup, gabungkan kembali untuk memenuhi kuota
            if ($newReviewers->count() >= $reviewerCountTarget) {
                $selectedReviewers = $newReviewers->take($reviewerCountTarget);
            } else {
                $needed = $reviewerCountTarget - $newReviewers->count();
                $alreadyMet = $candidates->whereIn('id', $prevReviewers)->take($needed);
                $selectedReviewers = $newReviewers->concat($alreadyMet);
            }

            foreach ($selectedReviewers as $reviewer) {
                $assignments[] = [
                    'reviewer_id' => $reviewer->id,
                    'reviewee_id' => $target->id,
                    'assessment_date' => $date,
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // 5. Simpan (Gunakan transaction untuk keamanan)
        \DB::transaction(function() use ($assignments, $date, $branchId) {
            // Hapus yang lama di periode & cabang tersebut jika ada (yang masih pending)
            $oldIds = \App\Models\ReviewerAssignment::where('assessment_date', $date)
                ->whereHas('reviewer', function($q) use ($branchId) {
                    $q->where('branch_id', $branchId);
                })
                ->where('status', 'pending')
                ->pluck('id');
            
            \App\Models\ReviewerAssignment::whereIn('id', $oldIds)->delete();

            // Insert baru
            foreach (array_chunk($assignments, 100) as $chunk) {
                \App\Models\ReviewerAssignment::insert($chunk);
            }
        });

        return redirect()->back()->with('success', "Berhasil generate matriks penilaian untuk periode " . \Carbon\Carbon::parse($date)->translatedFormat('F Y') . ". Total " . count($assignments) . " penugasan dibuat.");
    }

    // Metode lama tetap ada sementara untuk kompatibilitas jika diperlukan
    public function storeAssignments(Request $request) { /* ... */ }
    public function destroyAssignment(\App\Models\ReviewerAssignment $assignment) { /* ... */ }

    public function bulkDestroy(Request $request)
    {
        // ... (tetap seperti sebelumnya)
        $request->validate(['branch_id' => 'required|exists:branches,id', 'date' => 'required']);
        $normalizedDate = \Carbon\Carbon::parse($request->date)->startOfMonth()->format('Y-m-d');
        $assignmentIds = \App\Models\ReviewerAssignment::where('assessment_date', $normalizedDate)
            ->whereHas('reviewer', function($q) use ($request) { $q->where('branch_id', $request->branch_id); })
            ->pluck('id');
        \App\Models\ReviewerAssignment::whereIn('id', $assignmentIds)->delete();
        return redirect()->back()->with('success', 'Riwayat penugasan berhasil dihapus.');
    }
}
