<?php

namespace App\Http\Controllers\Pelatih;

use App\Http\Controllers\Controller;
use App\Models\Trial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrialController extends Controller
{
    public function index()
    {
        $coach = Auth::user();
        $trials = Trial::with('poolLocation')
            ->where('coach_id', $coach->id)
            ->orderBy('schedule_date', 'asc')
            ->orderBy('schedule_time', 'asc')
            ->get();

        return view('pelatih.trials.index', compact('trials'));
    }

    public function edit(Trial $trial)
    {
        if ($trial->coach_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('pelatih.trials.report', compact('trial'));
    }

    public function update(Request $request, Trial $trial)
    {
        if ($trial->coach_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'status' => 'required|in:hadir,absen',
            'report_note' => 'nullable|string',
        ]);

        $trial->update($validated);

        return redirect()->route('pelatih.trials.index')->with('success', 'Laporan trial berhasil disimpan.');
    }
}
