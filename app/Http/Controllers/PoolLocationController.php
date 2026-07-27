<?php

namespace App\Http\Controllers;

use App\Models\PoolLocation;
use Illuminate\Http\Request;

class PoolLocationController extends Controller
{
    public function index()
    {
        $locations = PoolLocation::all();
        return view('admin.pool_locations.index', compact('locations'));
    }

    public function create()
    {
        return view('admin.pool_locations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'package_name' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'meeting_count' => 'nullable|integer|min:1|max:8',
            'coach_fee' => 'required|integer|min:0',
            'cash_percentage' => 'required|numeric|min:0|max:100',
            'private_ticket_price' => 'nullable|integer|min:0',
            'semi_private_ticket_price' => 'nullable|integer|min:0',
        ]);

        PoolLocation::create($validated);
        return redirect()->route('admin.pool-locations.index')->with('success', 'Lokasi kolam berhasil ditambahkan.');
    }

    public function edit(PoolLocation $poolLocation)
    {
        return view('admin.pool_locations.edit', compact('poolLocation'));
    }

    public function update(Request $request, PoolLocation $poolLocation)
    {
        $validated = $request->validate([
            'package_name' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'meeting_count' => 'nullable|integer|min:1|max:8',
            'coach_fee' => 'required|integer|min:0',
            'cash_percentage' => 'required|numeric|min:0|max:100',
            'private_ticket_price' => 'nullable|integer|min:0',
            'semi_private_ticket_price' => 'nullable|integer|min:0',
        ]);

        $poolLocation->update($validated);
        return redirect()->route('admin.pool-locations.index')->with('success', 'Lokasi kolam berhasil diperbarui.');
    }

    public function destroy(PoolLocation $poolLocation)
    {
        $poolLocation->delete();
        return redirect()->route('admin.pool-locations.index')->with('success', 'Lokasi kolam berhasil dihapus.');
    }
}
