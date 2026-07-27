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
        
        // Asumsi format description: "Pembayaran Gaji Pelatih: [Nama Pelatih] ..."
        $histories = OperationalExpense::where('keyword', 'gaji_pelatih')
            ->where('description', 'like', '%Pembayaran Gaji Pelatih: ' . $coachName . '%')
            ->orderBy('expense_date', 'desc')
            ->get();

        return view('pelatih.payroll-history.index', compact('histories'));
    }
}
