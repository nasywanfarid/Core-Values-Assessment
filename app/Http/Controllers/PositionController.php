<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index()
    {
        $positions = \App\Models\Position::oldest()->get();
        return view('admin.positions.index', compact('positions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:positions',
        ]);
        
        \App\Models\Position::create($data);
        return redirect()->route('admin.positions.index')->with('success', 'Position berhasil ditambahkan');
    }

    public function update(Request $request, \App\Models\Position $position)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:positions,name,' . $position->id,
        ]);
        
        $position->update($data);
        return redirect()->route('admin.positions.index')->with('success', 'Position berhasil diupdate');
    }

    public function destroy(\App\Models\Position $position)
    {
        if ($position->users()->count() > 0) {
            return redirect()->route('admin.positions.index')->with('error', 'Position tidak dapat dihapus karena sedang digunakan oleh karyawan');
        }
        
        $position->delete();
        return redirect()->route('admin.positions.index')->with('success', 'Position berhasil dihapus');
    }
}
