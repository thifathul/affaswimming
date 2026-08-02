<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teams = Team::latest()->get();
        return view('master.teams.index', compact('teams'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $positions = \App\Models\Position::where('status', 'aktif')->get();
        return view('master.teams.create', compact('positions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'email' => 'nullable|email|unique:users,email',
            'role' => 'nullable|string|in:admin,pelatih',
        ]);

        $data = $request->except(['photo', 'email', 'role']);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('teams', 'public');
        }

        Team::create($data);

        // Jika form login diisi (email), buat user baru
        if ($request->filled('email')) {
            $role = $request->role;
            
            // Jika role tidak dipilih, coba tebak dari nama jabatan, default ke pelatih
            if (!$role) {
                $posName = strtolower($request->position);
                if (str_contains($posName, 'admin') || str_contains($posName, 'manajer') || str_contains($posName, 'pengelola')) {
                    $role = 'admin';
                } else {
                    $role = 'pelatih';
                }
            }

            // Coba cari position_id berdasarkan nama posisi dari tabel positions
            $positionModel = \App\Models\Position::where('name', $request->position)->first();
            
            \App\Models\User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt('password'),
                'role' => $role,
                'position_id' => $positionModel ? $positionModel->id : null,
                'status' => 'approved', // Pastikan status aktif
            ]);
        }

        return redirect()->route('master.teams.index')->with('success', 'Anggota tim berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Team $team)
    {
        $positions = \App\Models\Position::where('status', 'aktif')->get();
        return view('master.teams.edit', compact('team', 'positions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Team $team)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except('photo');

        if ($request->hasFile('photo')) {
            // Hapus foto lama jika ada
            if ($team->photo && Storage::disk('public')->exists($team->photo)) {
                Storage::disk('public')->delete($team->photo);
            }
            $data['photo'] = $request->file('photo')->store('teams', 'public');
        }

        $team->update($data);

        return redirect()->route('master.teams.index')->with('success', 'Data anggota tim berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team)
    {
        if ($team->photo && Storage::disk('public')->exists($team->photo)) {
            Storage::disk('public')->delete($team->photo);
        }

        $team->delete();

        return redirect()->route('master.teams.index')->with('success', 'Anggota tim berhasil dihapus.');
    }
}
