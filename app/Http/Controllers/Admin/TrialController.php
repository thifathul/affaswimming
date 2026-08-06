<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trial;
use App\Models\User;
use App\Models\PoolLocation;
use Illuminate\Http\Request;

class TrialController extends Controller
{
    public function index(Request $request)
    {
        $query = Trial::with(['coach', 'poolLocation']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('contact_number', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('schedule_date', $request->date);
        }

        $trials = $query->latest()->get();
        
        return view('admin.trials.index', compact('trials'));
    }

    public function create()
    {
        $coaches = User::where('role', 'pelatih')->where('status', 'approved')->get();
        $poolLocations = PoolLocation::all()->unique('name');
        return view('admin.trials.create', compact('coaches', 'poolLocations'));
    }

    public function store(Request $request)
    {
        if ($request->has('payment_amount')) {
            $request->merge([
                'payment_amount' => str_replace('.', '', $request->payment_amount)
            ]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'required|integer|min:1',
            'gender' => 'required|in:L,P',
            'school' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:255',
            'pool_location_id' => 'required|exists:pool_locations,id',
            'coach_id' => 'required|exists:users,id',
            'schedule_date' => 'required|date',
            'schedule_time' => 'required',
            'payment_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
        ]);

        $pool = PoolLocation::find($validated['pool_location_id']);
        
        $coachSalary = $pool->coach_fee;
        $poolName = strtolower($pool->name);
        $poolTicket = 0;
        
        if (str_contains($poolName, 'saraga')) {
            $poolTicket = ($pool->private_ticket_price ?? 80000);
        } elseif (str_contains($poolName, 'cipaku')) {
            $poolTicket = 70000;
        }

        $cashCut = 0;
        $profitCut = $validated['payment_amount'] - $coachSalary;
        if ($poolTicket > 0) {
            $profitCut -= $poolTicket;
        }

        $transaction = \App\Models\Transaction::create([
            'manual_student_name' => '[Trial] ' . $validated['name'],
            'practice_start_date' => $validated['schedule_date'],
            'pool_location_id' => $validated['pool_location_id'],
            'amount' => $validated['payment_amount'],
            'status' => 'approved',
            'payment_method' => $validated['payment_method'],
            'class_type' => 'private',
            'coach_salary_cut' => $coachSalary,
            'pool_ticket_cut' => $poolTicket,
            'cash_cut' => $cashCut,
            'profit_cut' => $profitCut,
        ]);
        
        $validated['transaction_id'] = $transaction->id;
        
        unset($validated['payment_amount']);
        unset($validated['payment_method']);

        Trial::create($validated);

        return redirect()->route('admin.trials.index')->with('success', 'Data trial berhasil ditambahkan.');
    }

    public function edit(Trial $trial)
    {
        $coaches = User::where('role', 'pelatih')->where('status', 'approved')->get();
        $poolLocations = PoolLocation::all()->unique('name');
        return view('admin.trials.edit', compact('trial', 'coaches', 'poolLocations'));
    }

    public function update(Request $request, Trial $trial)
    {
        if ($request->has('payment_amount')) {
            $request->merge([
                'payment_amount' => str_replace('.', '', $request->payment_amount)
            ]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'required|integer|min:1',
            'gender' => 'required|in:L,P',
            'school' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:255',
            'pool_location_id' => 'required|exists:pool_locations,id',
            'coach_id' => 'required|exists:users,id',
            'schedule_date' => 'required|date',
            'schedule_time' => 'required',
            'payment_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
        ]);
        
        $pool = PoolLocation::find($validated['pool_location_id']);
        
        $coachSalary = $pool->coach_fee;
        $poolName = strtolower($pool->name);
        $poolTicket = 0;
        
        if (str_contains($poolName, 'saraga')) {
            $poolTicket = ($pool->private_ticket_price ?? 80000);
        } elseif (str_contains($poolName, 'cipaku')) {
            $poolTicket = 70000;
        }

        $cashCut = 0;
        $profitCut = $validated['payment_amount'] - $coachSalary;
        if ($poolTicket > 0) {
            $profitCut -= $poolTicket;
        }
        
        $transactionData = [
            'manual_student_name' => '[Trial] ' . $validated['name'],
            'practice_start_date' => $validated['schedule_date'],
            'pool_location_id' => $validated['pool_location_id'],
            'amount' => $validated['payment_amount'],
            'payment_method' => $validated['payment_method'],
            'coach_salary_cut' => $coachSalary,
            'pool_ticket_cut' => $poolTicket,
            'cash_cut' => $cashCut,
            'profit_cut' => $profitCut,
        ];
        
        if ($trial->transaction_id && $trial->transaction) {
            $trial->transaction->update($transactionData);
        } else {
            $transactionData['status'] = 'approved';
            $transactionData['class_type'] = 'private';
            $transaction = \App\Models\Transaction::create($transactionData);
            $validated['transaction_id'] = $transaction->id;
        }

        unset($validated['payment_amount']);
        unset($validated['payment_method']);

        $trial->update($validated);

        return redirect()->route('admin.trials.index')->with('success', 'Data trial berhasil diperbarui.');
    }

    public function destroy(Trial $trial)
    {
        $trial->delete();
        return redirect()->route('admin.trials.index')->with('success', 'Data trial berhasil dihapus.');
    }
}
