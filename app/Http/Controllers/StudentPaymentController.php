<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\PoolLocation;

class StudentPaymentController extends Controller
{
    public function index()
    {
        $student = auth()->user()->student;
        $transactions = [];
        if ($student) {
            $transactions = $student->transactions()->latest()->get();
        }
        return view('student.payments.index', compact('transactions'));
    }

    public function create()
    {
        $poolLocations = PoolLocation::all();
        return view('student.payments.create', compact('poolLocations'));
    }

    public function store(Request $request)
    {
        $student = auth()->user()->student;
        if (!$student) {
            return redirect()->back()->with('error', 'Data murid tidak ditemukan. Pastikan profil murid sudah lengkap.');
        }

        $request->merge([
            'amount' => $request->amount ? str_replace('.', '', $request->amount) : null,
        ]);

        $validated = $request->validate([
            'pool_location_id' => 'required|exists:pool_locations,id',
            'class_type' => 'required|in:private,semi_private',
            'amount' => 'required|numeric|min:0',
            'practice_start_date' => 'required|date',
            'proof_of_payment' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('proof_of_payment')) {
            $path = $request->file('proof_of_payment')->store('payments', 'public');
            $validated['proof_of_payment'] = $path;
        }

        $validated['student_id'] = $student->id;
        $validated['status'] = 'pending';

        Transaction::create($validated);

        return redirect()->route('student.payments.index')->with('success', 'Bukti pembayaran berhasil diunggah dan sedang menunggu persetujuan.');
    }

    public function receipt(Transaction $transaction)
    {
        $user = auth()->user();
        $isAuthorized = false;

        if (in_array($user->role, ['admin', 'master'])) {
            $isAuthorized = true;
        } elseif ($user->role === 'murid' && $user->student && $transaction->student_id === $user->student->id) {
            $isAuthorized = true;
        }

        if (!$isAuthorized || $transaction->status !== 'approved') {
            abort(403, 'Akses ditolak atau pembayaran belum disetujui.');
        }

        return view('student.payments.receipt', compact('transaction'));
    }
}
