<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Student;
use App\Models\OperationalExpense;
use Carbon\Carbon;

class FinanceController extends Controller
{
    public function payments()
    {
        $transactions = Transaction::with(['student', 'poolLocation'])->latest()->get();
        return view('finance.payments.index', compact('transactions'));
    }

    public function create()
    {
        $poolLocations = \App\Models\PoolLocation::all();
        $students = Student::with('user')->get();
        return view('finance.payments.create', compact('poolLocations', 'students'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'amount' => $request->amount ? str_replace('.', '', $request->amount) : null,
            'credit' => $request->credit ? str_replace('.', '', $request->credit) : null,
        ]);

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'pool_location_id' => 'required|exists:pool_locations,id',
            'class_type' => 'required|in:private,semi_private',
            'amount' => 'required|numeric|min:0',
            'credit' => 'nullable|numeric|min:0',
            'practice_start_date' => 'required|date',
            'proof_of_payment' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'notes' => 'nullable|string',
        ]);

        if ($request->hasFile('proof_of_payment')) {
            $path = $request->file('proof_of_payment')->store('payments', 'public');
            $validated['proof_of_payment'] = $path;
        }

        $validated['status'] = 'pending';
        $validated['credit'] = $request->credit ?? 0;

        Transaction::create($validated);

        return redirect()->route('finance.payments.index')->with('success', 'Pembelian paket berhasil dibuat dan menunggu approval.');
    }

    public function approve(Request $request, Transaction $transaction)
    {
        if ($transaction->status !== 'pending') {
            return redirect()->back()->with('error', 'Transaksi ini sudah diproses.');
        }

        $request->merge([
            'credit' => $request->credit ? str_replace('.', '', $request->credit) : null,
        ]);

        $validated = $request->validate([
            'payment_method' => 'required|string',
            'credit' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $pool = $transaction->poolLocation;

        // Hitung Gaji
        $meetings = $pool->meeting_count ?? 4;
        $coachSalary = $pool->coach_fee * $meetings;

        // Hitung Tiket
        $poolTicket = 0;
        $poolName = strtolower($pool->name);
        if (str_contains($poolName, 'saraga')) {
            if ($transaction->class_type === 'private') {
                $poolTicket = ($pool->private_ticket_price ?? 80000);
            } else {
                $poolTicket = ($pool->semi_private_ticket_price ?? 40000);
            }
        } elseif (str_contains($poolName, 'cipaku')) {
            $poolTicket = 70000; // Default cipaku, can be adjusted
        }

        // Hitung Kas
        $cashCut = 0;
        if (str_contains($poolName, 'hv')) {
            $cashCut = $transaction->amount * 0.10;
        } else {
            $cashCut = $transaction->amount * ($pool->cash_percentage / 100);
        }

        // Hitung Keuntungan
        $profitCut = $transaction->amount - $coachSalary - $cashCut;
        if ($poolTicket > 0) {
            $profitCut -= $poolTicket;
        }

        // Update Transaction
        $updateData = [
            'status' => 'approved',
            'payment_method' => $validated['payment_method'],
            'coach_salary_cut' => $coachSalary,
            'pool_ticket_cut' => $poolTicket,
            'cash_cut' => $cashCut,
            'profit_cut' => $profitCut,
        ];

        if (isset($validated['credit'])) {
            $updateData['credit'] = $validated['credit'];
        }

        if (!empty($validated['notes'])) {
            $newNotes = $transaction->notes ? $transaction->notes . "\n" : "";
            $newNotes .= "Catatan Approval: " . $validated['notes'];
            $updateData['notes'] = $newNotes;
        }

        $transaction->update($updateData);

        // Update Student Package Info
        $activeUntil = Carbon::parse($transaction->practice_start_date)->addMonth();
        $transaction->student->update([
            'package_active_until' => $activeUntil,
        ]);
        
        $meetings = $transaction->poolLocation->meeting_count ?? 4;
        $transaction->student->increment('remaining_meetings', $meetings); // Tambahkan jumlah pertemuan sesuai paket

        return redirect()->back()->with('success', 'Pembayaran berhasil disetujui dan paket telah diaktifkan.');
    }

    public function reject(Transaction $transaction)
    {
        if ($transaction->status !== 'pending') {
            return redirect()->back()->with('error', 'Transaksi ini sudah diproses.');
        }

        $transaction->update(['status' => 'rejected']);

        return redirect()->back()->with('success', 'Pembayaran ditolak.');
    }

    public function settle(Request $request, Transaction $transaction)
    {
        if ($transaction->credit <= 0) {
            return redirect()->back()->with('error', 'Transaksi ini tidak memiliki hutang/kredit.');
        }

        $validated = $request->validate([
            'payment_method' => 'required|string',
            'settle_note' => 'required|string',
        ]);

        $newAmount = $transaction->amount + $transaction->credit;
        $pool = $transaction->poolLocation;
        $poolName = strtolower($pool->name);

        // Recalculate Cash Cut
        $cashCut = 0;
        if (str_contains($poolName, 'hv')) {
            $cashCut = $newAmount * 0.10;
        } else {
            $cashCut = $newAmount * ($pool->cash_percentage / 100);
        }

        // Recalculate Profit
        $profitCut = $newAmount - $transaction->coach_salary_cut - $cashCut;
        if ($transaction->pool_ticket_cut > 0) {
            $profitCut -= $transaction->pool_ticket_cut;
        }

        $newNotes = $transaction->notes ? $transaction->notes . "\n" : "";
        $newNotes .= "Pelunasan sebesar Rp " . number_format($transaction->credit, 0, ',', '.') . " via " . $validated['payment_method'] . ". Penerima/Catatan: " . $validated['settle_note'];

        $transaction->update([
            'amount' => $newAmount,
            'credit' => 0,
            'cash_cut' => $cashCut,
            'profit_cut' => $profitCut,
            'notes' => $newNotes,
        ]);

        return redirect()->back()->with('success', 'Pembayaran hutang berhasil dilunaskan dan saldo telah diperbarui.');
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();
        return redirect()->back()->with('success', 'Data pembayaran berhasil dihapus.');
    }

    public function unpaid()
    {
        // Hanya tampilkan murid yang punya transaksi/riwayat latihan (aktif) namun billing <= 0
        $students = \App\Models\Student::where('remaining_meetings', '<=', 0)
            ->whereHas('transactions', function($q) {
                $q->where('status', 'approved');
            })
            ->with(['user', 'attendances' => function($q) {
                $q->latest()->take(1);
            }])
            ->get();

        return view('finance.unpaid.index', compact('students'));
    }

    public function expenses(Request $request)
    {
        $expenses = OperationalExpense::latest()->get();

        // Rekap Pemasukan Berdasarkan Kolam
        $query = \App\Models\Transaction::where('transactions.status', 'approved');
        
        if ($request->filled('month')) {
            $month = $request->month;
            $year = substr($month, 0, 4);
            $mon = substr($month, 5, 2);
            $query->whereYear('transactions.updated_at', $year)->whereMonth('transactions.updated_at', $mon);
        }

        $poolSummaries = $query->join('pool_locations', 'transactions.pool_location_id', '=', 'pool_locations.id')
            ->selectRaw('
                pool_locations.name as pool_name,
                SUM(transactions.amount) as total_amount,
                SUM(transactions.coach_salary_cut) as total_coach_fee,
                SUM(transactions.cash_cut) as total_cash,
                SUM(transactions.pool_ticket_cut) as total_ticket,
                SUM(transactions.profit_cut) as total_profit
            ')
            ->groupBy('pool_locations.name')
            ->get();

        $expenseQuery = \App\Models\OperationalExpense::query();
        if ($request->filled('month')) {
            $month = $request->month;
            $year = substr($month, 0, 4);
            $mon = substr($month, 5, 2);
            $expenseQuery->whereYear('expense_date', $year)->whereMonth('expense_date', $mon);
        }

        $expenseSums = $expenseQuery->join('pool_locations', 'operational_expenses.pool_location_id', '=', 'pool_locations.id')
            ->selectRaw('
                pool_locations.name as pool_name,
                operational_expenses.keyword,
                SUM(operational_expenses.amount) as total_expense
            ')
            ->groupBy('pool_locations.name', 'operational_expenses.keyword')
            ->get();

        $poolSummaries = $poolSummaries->map(function ($summary) use ($expenseSums) {
            $poolName = $summary->pool_name;
            
            $gajiExpense = $expenseSums->where('pool_name', $poolName)->where('keyword', 'gaji')->sum('total_expense');
            $tiketExpense = $expenseSums->where('pool_name', $poolName)->where('keyword', 'tiket')->sum('total_expense');
            $kasExpense = $expenseSums->where('pool_name', $poolName)->where('keyword', 'kas')->sum('total_expense');
            $lainnyaExpense = $expenseSums->where('pool_name', $poolName)->where('keyword', 'lainnya')->sum('total_expense');

            $summary->net_coach_fee = $summary->total_coach_fee - $gajiExpense;
            $summary->net_ticket = $summary->total_ticket - $tiketExpense;
            $summary->net_cash = $summary->total_cash - $kasExpense;
            $summary->net_profit = $summary->total_profit - $lainnyaExpense;
            
            return $summary;
        });

        $poolLocations = \App\Models\PoolLocation::select('id', 'name')->get()->unique('name');
        
        $expensesQueryBuilder = OperationalExpense::with('poolLocation');
        if ($request->filled('month')) {
            $expensesQueryBuilder->whereYear('expense_date', $year)->whereMonth('expense_date', $mon);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $expensesQueryBuilder->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('keyword', 'like', "%{$search}%")
                  ->orWhereHas('poolLocation', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }
        $expenses = $expensesQueryBuilder->latest()->get();

        return view('finance.expenses.index', compact('expenses', 'poolSummaries', 'poolLocations'));
    }

    public function storeIncome(Request $request)
    {
        $validated = $request->validate([
            'manual_student_name' => 'required|string|max:255',
            'practice_start_date' => 'required|date',
            'payment_date' => 'required|date',
            'pool_location_id' => 'required|exists:pool_locations,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
        ]);

        $pool = \App\Models\PoolLocation::find($validated['pool_location_id']);

        // Hitung Gaji
        $meetings = $pool->meeting_count ?? 4;
        $coachSalary = $pool->coach_fee * $meetings;

        // Hitung Tiket
        $poolTicket = 0;
        $poolName = strtolower($pool->name);
        if (str_contains($poolName, 'saraga')) {
            $poolTicket = ($pool->private_ticket_price ?? 80000);
        } elseif (str_contains($poolName, 'cipaku')) {
            $poolTicket = 70000;
        }

        // Hitung Kas
        $cashCut = 0;
        if (str_contains($poolName, 'hv')) {
            $cashCut = $validated['amount'] * 0.10;
        } else {
            $cashCut = $validated['amount'] * ($pool->cash_percentage / 100);
        }

        // Hitung Keuntungan
        $profitCut = $validated['amount'] - $coachSalary - $cashCut;
        if ($poolTicket > 0) {
            $profitCut -= $poolTicket;
        }

        $transaction = Transaction::create([
            'manual_student_name' => $validated['manual_student_name'],
            'practice_start_date' => $validated['practice_start_date'],
            'pool_location_id' => $validated['pool_location_id'],
            'amount' => $validated['amount'],
            'status' => 'approved',
            'payment_method' => $validated['payment_method'],
            'class_type' => 'private', // default
            'coach_salary_cut' => $coachSalary,
            'pool_ticket_cut' => $poolTicket,
            'cash_cut' => $cashCut,
            'profit_cut' => $profitCut,
        ]);
        
        $transaction->timestamps = false;
        $transaction->created_at = $validated['payment_date'] . ' ' . now()->format('H:i:s');
        $transaction->updated_at = $validated['payment_date'] . ' ' . now()->format('H:i:s');
        $transaction->save();

        return redirect()->back()->with('success', 'Pemasukan berhasil ditambahkan.');
    }

    public function incomes(Request $request)
    {
        $query = Transaction::with(['student.user', 'poolLocation'])->where('status', 'approved');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('student.user', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })->orWhereHas('poolLocation', function($q) use ($search) {
                    $q->where('package_name', 'like', "%{$search}%")
                      ->orWhere('name', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('month')) {
            $month = $request->month;
            $year = substr($month, 0, 4);
            $mon = substr($month, 5, 2);
            $query->whereYear('updated_at', $year)->whereMonth('updated_at', $mon);
        }

        $incomes = $query->latest()->get();
        $poolLocations = \App\Models\PoolLocation::all();
        return view('finance.incomes.index', compact('incomes', 'poolLocations'));
    }



    public function destroyIncome(Transaction $transaction)
    {
        if ($transaction->proof_of_payment) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($transaction->proof_of_payment);
        }
        $transaction->delete();

        return redirect()->route('finance.incomes.index')->with('success', 'Data pemasukan berhasil dihapus.');
    }

    public function profit(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        
        $incomesQuery = Transaction::with('student.user')->where('status', 'approved');
        $expensesQuery = OperationalExpense::query();

        if ($month) {
            $year = substr($month, 0, 4);
            $mon = substr($month, 5, 2);
            $incomesQuery->whereYear('updated_at', $year)->whereMonth('updated_at', $mon);
            $expensesQuery->whereYear('expense_date', $year)->whereMonth('expense_date', $mon);
        }

        $incomes = $incomesQuery->get()->map(function($item) {
            return [
                'date' => $item->updated_at->format('Y-m-d'),
                'description' => 'Pembelian Paket - ' . ($item->student->user->name ?? 'Murid'),
                'income' => $item->profit_cut,
                'expense' => 0,
                'type' => 'income'
            ];
        });

        $expenses = $expensesQuery->get()->map(function($item) {
            return [
                'date' => $item->expense_date,
                'description' => $item->description,
                'income' => 0,
                'expense' => $item->amount,
                'type' => 'expense'
            ];
        });

        $transactions = $incomes->concat($expenses)->sortBy('date')->values();

        $previousBalance = 0;
        if ($month) {
            $year = substr($month, 0, 4);
            $mon = substr($month, 5, 2);
            $prevIncomes = Transaction::where('status', 'approved')->where('updated_at', '<', "$year-$mon-01")->sum('profit_cut');
            $prevExpenses = OperationalExpense::where('expense_date', '<', "$year-$mon-01")->sum('amount');
            $previousBalance = $prevIncomes - $prevExpenses;
        }

        $running_balance = $previousBalance;
        $profit_data = $transactions->map(function($item) use (&$running_balance) {
            $running_balance += $item['income'] - $item['expense'];
            $item['balance'] = $running_balance;
            return $item;
        });

        return view('finance.profit.index', compact('profit_data', 'month', 'previousBalance'));
    }

    public function exportProfit(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        
        $incomesQuery = Transaction::with('student.user')->where('status', 'approved');
        $expensesQuery = OperationalExpense::query();

        if ($month) {
            $year = substr($month, 0, 4);
            $mon = substr($month, 5, 2);
            $incomesQuery->whereYear('updated_at', $year)->whereMonth('updated_at', $mon);
            $expensesQuery->whereYear('expense_date', $year)->whereMonth('expense_date', $mon);
        }

        $incomes = $incomesQuery->get()->map(function($item) {
            return [
                'date' => $item->updated_at->format('Y-m-d'),
                'description' => 'Pembelian Paket - ' . ($item->student->user->name ?? 'Murid'),
                'income' => $item->profit_cut,
                'expense' => 0,
                'type' => 'income'
            ];
        });

        $expenses = $expensesQuery->get()->map(function($item) {
            return [
                'date' => $item->expense_date,
                'description' => $item->description,
                'income' => 0,
                'expense' => $item->amount,
                'type' => 'expense'
            ];
        });

        $transactions = $incomes->concat($expenses)->sortBy('date')->values();

        $previousBalance = 0;
        if ($month) {
            $year = substr($month, 0, 4);
            $mon = substr($month, 5, 2);
            $prevIncomes = Transaction::where('status', 'approved')->where('updated_at', '<', "$year-$mon-01")->sum('profit_cut');
            $prevExpenses = OperationalExpense::where('expense_date', '<', "$year-$mon-01")->sum('amount');
            $previousBalance = $prevIncomes - $prevExpenses;
        }

        $running_balance = $previousBalance;
        $profit_data = $transactions->map(function($item) use (&$running_balance) {
            $running_balance += $item['income'] - $item['expense'];
            $item['balance'] = $running_balance;
            return $item;
        });

        $fileName = "Laporan_Profit_Laba_Rugi_{$month}.csv";
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Tanggal', 'Keterangan Transaksi', 'Pemasukan', 'Pengeluaran', 'Profit / Saldo Akhir');

        $callback = function() use($profit_data, $columns, $previousBalance, $month) {
            // output with UTF-8 BOM for Excel to read correctly
            $file = fopen('php://output', 'w');
            fputs($file, $bom =(chr(0xEF) . chr(0xBB) . chr(0xBF)));
            fputcsv($file, $columns, ';');
            
            if ($month) {
                fputcsv($file, [
                    \Carbon\Carbon::parse($month.'-01')->format('Y-m-d'), 
                    'Saldo Awal per ' . \Carbon\Carbon::parse($month.'-01')->format('M Y'), 
                    0, 
                    0, 
                    $previousBalance
                ], ';');
            }

            foreach ($profit_data as $item) {
                fputcsv($file, [
                    \Carbon\Carbon::parse($item['date'])->format('Y-m-d'),
                    $item['description'],
                    $item['income'],
                    $item['expense'],
                    $item['balance']
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function storeExpense(Request $request)
    {
        $validated = $request->validate([
            'pool_location_id' => 'nullable|exists:pool_locations,id',
            'keyword' => 'required|string|max:255',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
        ]);

        OperationalExpense::create($validated);

        return redirect()->route('finance.expenses.index')->with('success', 'Pengeluaran operasional berhasil dicatat.');
    }

    public function destroyExpense(\App\Models\OperationalExpense $expense)
    {
        if ($expense->proof_file) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($expense->proof_file);
        }
        $expense->delete();

        return redirect()->route('finance.expenses.index')->with('success', 'Pengeluaran operasional berhasil dihapus.');
    }
}
