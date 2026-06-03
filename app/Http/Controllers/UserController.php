<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = \App\Models\User::with(['branch', 'division', 'position']);
        
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('division_id')) {
            $query->where('division_id', $request->division_id);
        }

        if ($request->filled('position_id')) {
            $query->where('position_id', $request->position_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $sort = $request->get('sort', 'name');
        $direction = $request->get('direction', 'asc');
        
        // Validate sort column to prevent SQL injection
        if (in_array($sort, ['name', 'email'])) {
            $query->orderBy($sort, $direction);
        } elseif ($sort === 'division') {
            $query->join('divisions', 'users.division_id', '=', 'divisions.id')
                  ->orderBy('divisions.name', $direction)
                  ->select('users.*');
        } elseif ($sort === 'position') {
            $query->leftJoin('positions', 'users.position_id', '=', 'positions.id')
                  ->orderBy('positions.name', $direction)
                  ->select('users.*');
        } else {
            $query->orderBy('name', 'asc');
        }
        
        $users = $query->paginate(25)->withQueryString();
        $branches = \App\Models\Branch::all();
        $divisions = \App\Models\Division::all();
        $positions = \App\Models\Position::all();
        return view('admin.users.index', compact('users', 'branches', 'divisions', 'positions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'branch_id' => 'required|exists:branches,id',
            'division_id' => 'required|exists:divisions,id',
            'position_id' => 'nullable|exists:positions,id',
            'role' => 'nullable|string|in:admin,hr,direktur,karyawan',
        ]);
        
        $data['password'] = bcrypt($data['password']);
        $data['role'] = (auth()->user()->role === 'admin' && isset($data['role'])) ? $data['role'] : 'karyawan';
        
        \App\Models\User::create($data);
        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan');
    }

    public function update(Request $request, \App\Models\User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'branch_id' => 'required|exists:branches,id',
            'division_id' => 'required|exists:divisions,id',
            'position_id' => 'nullable|exists:positions,id',
            'role' => 'nullable|string|in:admin,hr,direktur,karyawan',
        ]);
        
        if (auth()->user()->role !== 'admin') {
            unset($data['role']);
        }

        if ($request->filled('password')) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        
        $user->update($data);
        return redirect()->route('admin.users.index')->with('success', 'Data karyawan berhasil diupdate');
    }

    public function destroy(\App\Models\User $user)
    {
        if (auth()->user()->role === 'hr') abort(403, 'Unauthorized action.');
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User dihapus');
    }
}
