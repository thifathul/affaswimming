<?php

namespace App\Http\Controllers\Pelatih;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OperationalExpense;

class PayrollHistoryController extends Controller
{
    public function history()
    {
        $coachName = auth()->user()->name;
        
        // Asumsi format description: 
        // 1. Lama: "Pembayaran Gaji Pelatih: [Nama Pelatih] ..."
        // 2. Baru: "Penggajian "[Nama Pelatih]" ..."
        $histories = OperationalExpense::whereIn('keyword', ['gaji', 'gaji_pelatih'])
            ->where(function($q) use ($coachName) {
                $q->where('description', 'like', '%Pembayaran Gaji Pelatih: ' . $coachName . '%')
                  ->orWhere('description', 'like', '%Penggajian "' . $coachName . '"%');
            })
            ->orderBy('expense_date', 'desc')
            ->get();

        return view('pelatih.payroll-history.index', compact('histories'));
    }
}
