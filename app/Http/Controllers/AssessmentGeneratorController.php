<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\ReviewerAssignment;
use App\Models\User;
use App\Models\InteractionMatrix;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AssessmentGeneratorController extends Controller
{
    public function index(Request $request)
    {
        $branches = Branch::all();
        
        $history = ReviewerAssignment::selectRaw('assessment_date, branches.name as branch_name, branches.id as branch_id, count(*) as total_assignments, round(count(*) / count(distinct reviewee_id)) as reviewer_count, sum(case when status="completed" then 1 else 0 end) as completed_assignments, min(cast(is_active as unsigned)) as all_active')
            ->join('users', 'reviewer_assignments.reviewer_id', '=', 'users.id')
            ->join('branches', 'users.branch_id', '=', 'branches.id')
            ->groupBy('assessment_date', 'branches.id', 'branches.name')
            ->orderBy('assessment_date', 'desc')
            ->paginate(10);

        return view('admin.interaction-matrices.generate', compact('branches', 'history'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'date' => 'required',
            'count' => 'required|integer|in:3,4,5',
        ]);

        $branchId = $request->branch_id;
        $date = Carbon::parse($request->date)->startOfMonth()->format('Y-m-d');
        $reviewerCountTarget = $request->count;

        // 1. Ambil Relasi Divisi GLOBAL
        $matrix = InteractionMatrix::whereNull('branch_id')->get();
        if ($matrix->isEmpty()) {
            return redirect()->back()->with('error', 'Gagal generate: Belum ada relasi divisi yang diatur di menu Matriks Penilai.');
        }

        // 2. Ambil Semua Karyawan di Cabang Terpilih
        $employees = User::where('branch_id', $branchId)
            ->whereIn('role', ['karyawan', 'direktur'])
            ->get();

        // 3. Ambil History Bulan Sebelumnya untuk Rotasi
        $previousDate = Carbon::parse($date)->subMonth()->format('Y-m-d');
        $prevHistory = ReviewerAssignment::where('assessment_date', $previousDate)
            ->get()
            ->groupBy('reviewee_id')
            ->map(function($items) {
                return $items->pluck('reviewer_id')->toArray();
            });

        $assignments = [];
        $reviewerLoad = []; // Untuk load balancing penilai
        foreach ($employees as $emp) {
            $reviewerLoad[$emp->id] = 0;
        }

        // 4. Filter Target: Karyawan yang berada di divisi "Direktur" tidak dinilai
        $direkturDivId = \App\Models\Division::where('name', 'Direktur')->value('id');
        $targets = $employees->where('division_id', '!=', $direkturDivId);

        foreach ($targets as $target) {
            // Cari divisi penilai yang diizinkan
            $allowedReviewerDivIds = $matrix->where('target_division_id', $target->division_id)
                ->pluck('reviewer_division_id')
                ->toArray();
            
            // Kandidat penilai potensial (Tambahkan shuffle agar acak)
            $candidates = $employees->whereIn('division_id', $allowedReviewerDivIds)
                ->where('id', '!=', $target->id)
                ->shuffle() // <--- Diacak di sini
                ->values();

            if ($candidates->count() < $reviewerCountTarget) {
                return redirect()->back()->with('error', "Gagal: Karyawan {$target->name} hanya memiliki {$candidates->count()} calon penilai, kuota diminta {$reviewerCountTarget}.");
            }

            // AMBIL HISTORY BULAN LALU
            $oldReviewerIds = $prevHistory->get($target->id, []);

            // ROTASI LOGIC:
            // Urutkan kandidat berdasarkan:
            // 1. Apakah dia penilai baru? (Dahulukan yang TIDAK ada di history bulan lalu)
            // 2. Siapa yang beban tugasnya (reviewerLoad) paling rendah?
            $selectedReviewers = $candidates->sort(function($a, $b) use ($oldReviewerIds, $reviewerLoad) {
                $isAPrev = in_array($a->id, $oldReviewerIds) ? 1 : 0;
                $isBPrev = in_array($b->id, $oldReviewerIds) ? 1 : 0;
                
                if ($isAPrev !== $isBPrev) return $isAPrev <=> $isBPrev;
                return $reviewerLoad[$a->id] <=> $reviewerLoad[$b->id];
            })->take($reviewerCountTarget);

            foreach ($selectedReviewers as $reviewer) {
                $assignments[] = [
                    'reviewer_id' => $reviewer->id,
                    'reviewee_id' => $target->id,
                    'assessment_date' => $date,
                    'status' => 'pending',
                    'is_active' => false, // Status awal tertutup (jangan langsung terbuka)
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $reviewerLoad[$reviewer->id]++;
            }
        }

        // 5. Simpan dengan Pembersihan Total Cabang & Periode
        DB::transaction(function() use ($assignments, $date, $branchId) {
            $allIds = ReviewerAssignment::where('assessment_date', $date)
                ->whereHas('reviewer', function($q) use ($branchId) {
                    $q->where('branch_id', $branchId);
                })
                ->pluck('id');
            
            ReviewerAssignment::whereIn('id', $allIds)->delete();

            foreach (array_chunk($assignments, 100) as $chunk) {
                ReviewerAssignment::insert($chunk);
            }
        });

        return redirect()->back()->with('success', "Berhasil generate matriks periode " . Carbon::parse($date)->translatedFormat('F Y') . " dengan rotasi otomatis.");
    }

    public function toggleStatus($date, $branchId)
    {
        $assignments = ReviewerAssignment::where('assessment_date', $date)
            ->whereHas('reviewer', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })->get();

        if ($assignments->isEmpty()) return redirect()->back();

        $newStatus = !($assignments->first()->is_active);
        ReviewerAssignment::where('assessment_date', $date)
            ->whereHas('reviewer', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })->update(['is_active' => $newStatus]);

        return redirect()->back()->with('success', "Status penilaian berhasil diubah.");
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate(['branch_id' => 'required', 'date' => 'required']);
        ReviewerAssignment::where('assessment_date', $request->date)
            ->whereHas('reviewer', function($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            })->delete();

        return redirect()->back()->with('success', 'Data riwayat berhasil dihapus.');
    }
}
