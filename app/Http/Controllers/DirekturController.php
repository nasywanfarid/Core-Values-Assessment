<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DirekturController extends Controller
{
    public function index()
    {
        // Direktur logic is similar to Admin but read-only
        $users = \App\Models\User::where('role', 'karyawan')->get();
        $indicators = \App\Models\Indicator::all();
        
        $totalKaryawan = \App\Models\User::where('role', 'karyawan')->count();
        $totalDivisi = \App\Models\Division::count();
        $completedAssignments = \App\Models\ReviewerAssignment::where('status', 'completed')->count();
        $pendingAssignments = \App\Models\ReviewerAssignment::where('status', 'pending')->count();

        // Share the same admin view since it is a dashboard
        return view('admin.dashboard', compact(
            'totalKaryawan', 
            'totalDivisi', 
            'completedAssignments', 
            'pendingAssignments'
        ));
    }
}
