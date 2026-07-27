<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\TrainingReport;
use Carbon\Carbon;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $coaches = User::where('role', 'pelatih')->get();
        
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $selectedCoachId = $request->input('coach_id');

        $payrollData = [];
        $totalSalary = 0;
        $totalMeetings = 0;

        if ($selectedCoachId) {
            $reports = TrainingReport::with(['schedule.poolLocation'])
                ->where('coach_id', $selectedCoachId)
                ->where('coach_attendance', 'Hadir')
                ->whereBetween('training_date', [$startDate, $endDate])
                ->get();

            // Kelompokkan berdasarkan pool_location
            $grouped = $reports->groupBy(function ($report) {
                return $report->schedule && $report->schedule->poolLocation 
                    ? $report->schedule->poolLocation->name 
                    : 'Tidak Diketahui';
            });

            foreach ($grouped as $poolName => $poolReports) {
                $meetingsCount = $poolReports->count();
                // Ambil fee dari record pertama di grup
                $coachFee = $poolReports->first()->schedule->poolLocation->coach_fee ?? 0;
                $salary = $meetingsCount * $coachFee;

                $payrollData[] = [
                    'pool_name' => $poolName,
                    'meetings_count' => $meetingsCount,
                    'coach_fee' => $coachFee,
                    'salary' => $salary,
                ];

                $totalMeetings += $meetingsCount;
                $totalSalary += $salary;
            }
        }

        return view('finance.payroll.index', compact('coaches', 'startDate', 'endDate', 'selectedCoachId', 'payrollData', 'totalSalary', 'totalMeetings'));
    }

    public function paySalary(Request $request)
    {
        $validated = $request->validate([
            'coach_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'amount' => 'required|numeric|min:1',
            'proof_file' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'notes' => 'nullable|string',
        ]);

        $coach = User::findOrFail($validated['coach_id']);
        
        $path = null;
        if ($request->hasFile('proof_file')) {
            $path = $request->file('proof_file')->store('expenses', 'public');
        }

        $reports = TrainingReport::with(['schedule.poolLocation'])
            ->where('coach_id', $coach->id)
            ->where('coach_attendance', 'Hadir')
            ->whereBetween('training_date', [$validated['start_date'], $validated['end_date']])
            ->get();

        $grouped = $reports->groupBy(function ($report) {
            return $report->schedule && $report->schedule->poolLocation 
                ? $report->schedule->poolLocation->id 
                : null;
        });

        foreach ($grouped as $poolId => $poolReports) {
            $meetingsCount = $poolReports->count();
            $coachFee = $poolReports->first()->schedule->poolLocation->coach_fee ?? 0;
            $salary = $meetingsCount * $coachFee;

            if ($salary > 0) {
                $description = "Penggajian \"{$coach->name}\" (Periode: {$validated['start_date']} s/d {$validated['end_date']})";
                if ($validated['notes']) {
                    $description .= " - Catatan: " . $validated['notes'];
                }

                \App\Models\OperationalExpense::create([
                    'pool_location_id' => $poolId,
                    'keyword' => 'gaji',
                    'description' => $description,
                    'amount' => $salary,
                    'expense_date' => now()->format('Y-m-d'),
                    'proof_file' => $path,
                ]);
            }
        }

        return redirect()->route('finance.payroll.index', [
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'coach_id' => $validated['coach_id']
        ])->with('success', 'Pembayaran gaji berhasil dicatat sebagai pengeluaran operasional.');
    }
}
