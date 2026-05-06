<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DivisionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $divisions = \App\Models\Division::paginate(10);
        return view('admin.divisions.index', compact('divisions'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        \App\Models\Division::create($request->all());
        return redirect()->route('admin.divisions.index')->with('success', 'Divisi berhasil ditambahkan');
    }

    public function update(Request $request, \App\Models\Division $division)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $division->update($request->all());
        return redirect()->route('admin.divisions.index')->with('success', 'Divisi berhasil diupdate');
    }

    public function destroy(\App\Models\Division $division)
    {
        if (auth()->user()->role === 'hr') abort(403, 'Unauthorized action.');
        $division->delete();
        return redirect()->route('admin.divisions.index')->with('success', 'Divisi berhasil dihapus');
    }
}
