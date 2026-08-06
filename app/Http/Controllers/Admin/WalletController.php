<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    public function index(Request $request)
    {
        $query = User::whereIn('role', ['pelatih', 'murid'])->with('wallet');
        
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('role', 'like', '%' . $request->search . '%');
            });
        }
        
        $users = $query->paginate(20);
        return view('admin.wallets.index', compact('users'));
    }

    public function show(User $user)
    {
        if (!in_array($user->role, ['pelatih', 'murid'])) {
            return redirect()->route('admin.wallets.index')->with('error', 'Hanya Pelatih dan Murid yang memiliki akses E-Wallet.');
        }

        if (!$user->wallet) {
            $user->wallet()->create(['balance' => 0]);
            $user->load('wallet');
        }

        $transactions = $user->wallet->transactions()->latest()->paginate(20);
        
        return view('admin.wallets.show', compact('user', 'transactions'));
    }

    public function storeTransaction(Request $request, User $user)
    {
        if ($request->has('amount')) {
            $request->merge([
                'amount' => str_replace('.', '', $request->amount)
            ]);
        }

        $request->validate([
            'type' => 'required|in:deposit,withdraw,borrow,repay',
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string|max:255',
        ]);

        $amount = (float) $request->amount;
        $type = $request->type;
        
        $wallet = $user->wallet()->firstOrCreate([], ['balance' => 0]);

        DB::beginTransaction();
        try {
            if ($type === 'withdraw' && $wallet->balance < $amount) {
                return back()->with('error', 'Saldo tidak mencukupi untuk melakukan penarikan.');
            }
            
            // Allow repaying even if balance is >= 0? Maybe they want to deposit? Repay is technically a deposit, but logically if balance >= 0, they don't have debt.
            if ($type === 'repay' && $wallet->balance >= 0) {
                 return back()->with('error', 'Pelatih tidak memiliki pinjaman (kasbon) yang harus dibayar.');
            }

            $balanceChange = 0;
            switch ($type) {
                case 'deposit':
                case 'repay':
                    $balanceChange = $amount;
                    break;
                case 'withdraw':
                case 'borrow':
                    $balanceChange = -$amount;
                    break;
            }

            $wallet->transactions()->create([
                'type' => $type,
                'amount' => $amount,
                'description' => $request->description,
            ]);

            $wallet->balance += $balanceChange;
            $wallet->save();

            DB::commit();

            return redirect()->route('admin.wallets.show', $user->id)->with('success', 'Transaksi E-Wallet berhasil diproses.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat memproses transaksi: ' . $e->getMessage());
        }
    }
}
