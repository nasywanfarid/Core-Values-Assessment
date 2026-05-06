<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $branches = \App\Models\Branch::paginate(10);
        return view('admin.branches.index', compact('branches'));
    }

    public function create()
    {
        return view('admin.branches.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        \App\Models\Branch::create($request->all());
        return redirect()->route('admin.branches.index')->with('success', 'Cabang berhasil ditambahkan');
    }

    public function edit(\App\Models\Branch $branch)
    {
        return view('admin.branches.edit', compact('branch'));
    }

    public function update(Request $request, \App\Models\Branch $branch)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $branch->update($request->all());
        return redirect()->route('admin.branches.index')->with('success', 'Cabang berhasil diupdate');
    }

    public function destroy(\App\Models\Branch $branch)
    {
        if (auth()->user()->role === 'hr') abort(403, 'Unauthorized action.');
        $branch->delete();
        return redirect()->route('admin.branches.index')->with('success', 'Cabang berhasil dihapus');
    }
}
