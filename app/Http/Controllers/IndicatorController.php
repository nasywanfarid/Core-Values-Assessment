<?php

namespace App\Http\Controllers;

use App\Models\Indicator;
use Illuminate\Http\Request;

class IndicatorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $indicators = Indicator::all();
        return view('admin.indicators.index', compact('indicators'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.indicators.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'scale_1' => 'required|string',
            'scale_2' => 'required|string',
            'scale_3' => 'required|string',
            'scale_4' => 'required|string',
            'scale_5' => 'required|string',
        ]);

        Indicator::create($request->all());

        return redirect()->route('admin.indicators.index')->with('success', 'Indikator berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Indicator $indicator)
    {
        return view('admin.indicators.edit', compact('indicator'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Indicator $indicator)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'scale_1' => 'required|string',
            'scale_2' => 'required|string',
            'scale_3' => 'required|string',
            'scale_4' => 'required|string',
            'scale_5' => 'required|string',
        ]);

        $indicator->update($request->all());

        return redirect()->route('admin.indicators.index')->with('success', 'Indikator berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Indicator $indicator)
    {
        if (auth()->user()->role === 'hr') abort(403, 'Unauthorized action.');
        $indicator->delete();
        return redirect()->route('admin.indicators.index')->with('success', 'Indikator berhasil dihapus');
    }
}
