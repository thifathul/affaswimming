<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\SwimClass;
use App\Models\User;
use Illuminate\Http\Request;

class SwimClassController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $swimClasses = SwimClass::with('coaches')->withCount('students')->latest()->get();
        return view('master.swim_classes.index', compact('swimClasses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $coaches = User::where('role', 'pelatih')->get();
        return view('master.swim_classes.create', compact('coaches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->filled('schedule_hari') && $request->filled('schedule_jam')) {
            $request->merge(['schedule' => $request->schedule_hari . ', ' . $request->schedule_jam]);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'schedule' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif',
            'coach_ids' => 'nullable|array',
            'coach_ids.*' => 'exists:users,id',
        ]);

        $swimClass = SwimClass::create($request->only('name', 'schedule', 'description', 'status'));

        if ($request->has('coach_ids')) {
            $swimClass->coaches()->sync($request->coach_ids);
        }

        return redirect()->route('master.swim-classes.index')->with('success', 'Kelas berenang berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SwimClass $swimClass)
    {
        $coaches = User::where('role', 'pelatih')->get();
        $selectedCoaches = $swimClass->coaches->pluck('id')->toArray();
        return view('master.swim_classes.edit', compact('swimClass', 'coaches', 'selectedCoaches'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SwimClass $swimClass)
    {
        if ($request->filled('schedule_hari') && $request->filled('schedule_jam')) {
            $request->merge(['schedule' => $request->schedule_hari . ', ' . $request->schedule_jam]);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'schedule' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif',
            'coach_ids' => 'nullable|array',
            'coach_ids.*' => 'exists:users,id',
        ]);

        $swimClass->update($request->only('name', 'schedule', 'description', 'status'));

        if ($request->has('coach_ids')) {
            $swimClass->coaches()->sync($request->coach_ids);
        } else {
            $swimClass->coaches()->detach();
        }

        return redirect()->route('master.swim-classes.index')->with('success', 'Kelas berenang berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SwimClass $swimClass)
    {
        if ($swimClass->students()->count() > 0) {
            return redirect()->route('master.swim-classes.index')->with('error', 'Tidak dapat menghapus kelas yang masih memiliki murid.');
        }

        $swimClass->coaches()->detach();
        $swimClass->delete();

        return redirect()->route('master.swim-classes.index')->with('success', 'Kelas berenang berhasil dihapus.');
    }

    /**
     * Toggle the active status of the resource.
     */
    public function toggleStatus(SwimClass $swimClass)
    {
        $swimClass->update([
            'status' => $swimClass->status === 'aktif' ? 'nonaktif' : 'aktif'
        ]);

        $message = $swimClass->status === 'aktif' ? 'Status kelas berhasil diaktifkan.' : 'Status kelas berhasil dinonaktifkan.';
        
        return redirect()->route('master.swim-classes.index')->with('success', $message);
    }
}
