<?php

namespace App\Http\Controllers;

use App\Models\PoolLocation;
use Illuminate\Http\Request;

class PoolLocationController extends Controller
{
    public function index(Request $request)
    {
        $query = PoolLocation::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('package_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('location')) {
            $query->where('name', $request->location);
        }

        $locations = $query->paginate(10)->withQueryString();
        $allLocations = PoolLocation::select('name')->distinct()->orderBy('name')->pluck('name');

        return view('admin.pool_locations.index', compact('locations', 'allLocations'));
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
