<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RegistrationApprovalController extends Controller
{
    public function index()
    {
        $pendingUsers = \App\Models\User::where('role', 'murid')
            ->where('status', 'pending')
            ->with('student')
            ->latest()
            ->get();
            
        return view('admin.registrations.index', compact('pendingUsers'));
    }

    public function approve($id)
    {
        $user = \App\Models\User::findOrFail($id);
        $user->status = 'approved';
        $user->save();
        
        return redirect()->back()->with('success', 'Akun pendaftar berhasil disetujui.');
    }

    public function reject($id)
    {
        $user = \App\Models\User::findOrFail($id);
        
        // Menghapus data murid terkait jika ada
        if ($user->student) {
            $user->student->delete();
        }
        
        // Hapus akun karena ditolak
        $user->delete();
        
        return redirect()->back()->with('success', 'Pendaftaran ditolak dan data telah dihapus.');
    }
}
