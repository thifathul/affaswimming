<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Position;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $positions = Position::withCount('teams')->latest()->get();
        return view('master.positions.index', compact('positions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master.positions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_salary' => 'nullable|numeric|min:0',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        Position::create([
            'name' => $request->name,
            'description' => $request->description,
            'base_salary' => $request->base_salary,
            'status' => $request->status,
        ]);

        return redirect()->route('master.positions.index')->with('success', 'Kategori jabatan berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Position $position)
    {
        return view('master.positions.edit', compact('position'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Position $position)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_salary' => 'nullable|numeric|min:0',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $position->update([
            'name' => $request->name,
            'description' => $request->description,
            'base_salary' => $request->base_salary,
            'status' => $request->status,
        ]);

        return redirect()->route('master.positions.index')->with('success', 'Kategori jabatan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Position $position)
    {
        if ($position->users()->count() > 0) {
            return redirect()->route('master.positions.index')->with('error', 'Jabatan tidak dapat dihapus karena masih digunakan oleh pengguna.');
        }

        $position->delete();

        return redirect()->route('master.positions.index')->with('success', 'Kategori jabatan berhasil dihapus.');
    }

    /**
     * Toggle the active status of the position.
     */
    public function toggleStatus(Position $position)
    {
        $position->update([
            'status' => $position->status === 'aktif' ? 'nonaktif' : 'aktif'
        ]);

        $message = $position->status === 'aktif' ? 'Status jabatan berhasil diaktifkan.' : 'Status jabatan berhasil dinonaktifkan.';
        
        return redirect()->route('master.positions.index')->with('success', $message);
    }
}
