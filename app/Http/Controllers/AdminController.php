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

        return view('admin.dashboard', compact(
            'totalKaryawan', 
            'totalDivisi', 
            'completedAssignments', 
            'pendingAssignments'
        ));
    }
}
