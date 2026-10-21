<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\TrainingReport;
use App\Models\Trial;
use Carbon\Carbon;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $coaches = User::where('role', 'pelatih')->orderBy('name', 'asc')->get();
        
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $selectedCoachId = $request->input('coach_id');

        $payrollData = [];
        $totalSalary = 0;
        $totalMeetings = 0;
        $totalStudents = 0;

        if ($selectedCoachId) {
            $reports = TrainingReport::with([
                'schedule.poolLocation',
                'studentAttendances' => function ($q) {
                    $q->where('status', 'Hadir')->with('student');
                }
            ])
                ->where('coach_id', $selectedCoachId)
                ->where('coach_attendance', 'Hadir')
                ->whereBetween('training_date', [$startDate, $endDate])
                ->get();

            $trials = Trial::with('poolLocation')
                ->where('coach_id', $selectedCoachId)
                ->where('status', 'hadir')
                ->whereBetween('schedule_date', [$startDate, $endDate])
                ->get();

            // Kelompokkan reports berdasarkan pool_location id
            $reportsByPool = $reports->groupBy(function ($report) {
                return $report->schedule && $report->schedule->poolLocation 
                    ? $report->schedule->poolLocation->id 
                    : 0;
            });

            // Kelompokkan trials berdasarkan pool_location id
            $trialsByPool = $trials->groupBy(function ($trial) {
                return $trial->pool_location_id ?? 0;
            });

            $allPoolIds = $reportsByPool->keys()->merge($trialsByPool->keys())->unique();

            foreach ($allPoolIds as $poolId) {
                $poolReports = $reportsByPool->get($poolId, collect());
                $poolTrials = $trialsByPool->get($poolId, collect());

                $poolLocation = null;
                if ($poolReports->isNotEmpty() && $poolReports->first()->schedule) {
                    $poolLocation = $poolReports->first()->schedule->poolLocation;
                } elseif ($poolTrials->isNotEmpty()) {
                    $poolLocation = $poolTrials->first()->poolLocation;
                }

                $poolName = $poolLocation ? $poolLocation->name : 'Tidak Diketahui';
                $coachFee = $poolLocation->coach_fee ?? 0;

                // Hitung sesi reguler & kehadiran murid
                $regularMeetingsCount = $poolReports->count();
                $regularStudentsCount = $poolReports->sum(function ($report) {
                    return $report->studentAttendances->where('status', 'Hadir')->count();
                });

                // Hitung sesi trial & murid (1 trial hadir = 1 murid)
                $trialCount = $poolTrials->count();

                $totalMeetingsInPool = $regularMeetingsCount + $trialCount;
                $totalStudentsInPool = $regularStudentsCount + $trialCount;
                $salary = $totalStudentsInPool * $coachFee;

                $sessions = $poolReports->map(function ($report) use ($coachFee) {
                    $hadirAttendances = $report->studentAttendances->where('status', 'Hadir');
                    $hadirCount = $hadirAttendances->count();
                    return [
                        'type' => 'regular',
                        'raw_date' => $report->training_date,
                        'date' => \Carbon\Carbon::parse($report->training_date)->format('d/m/Y'),
                        'time' => $report->schedule ? substr($report->schedule->start_time, 0, 5) . ' - ' . substr($report->schedule->end_time, 0, 5) : '-',
                        'students' => $hadirAttendances->map(fn($sa) => $sa->student->name ?? 'Murid')->values()->all(),
                        'students_count' => $hadirCount,
                        'subtotal' => $hadirCount * $coachFee,
                    ];
                });

                $trialSessions = $poolTrials->map(function ($trial) use ($coachFee) {
                    return [
                        'type' => 'trial',
                        'raw_date' => $trial->schedule_date,
                        'date' => $trial->schedule_date->format('d/m/Y'),
                        'time' => $trial->schedule_time ? \Carbon\Carbon::parse($trial->schedule_time)->format('H:i') : '-',
                        'students' => ['[Trial] ' . $trial->name],
                        'students_count' => 1,
                        'subtotal' => 1 * $coachFee,
                    ];
                });

                $allSessions = $sessions->concat($trialSessions)->sortBy(function ($item) {
                    return Carbon::parse($item['raw_date'])->timestamp;
                })->values();

                $payrollData[] = [
                    'pool_name' => $poolName,
                    'meetings_count' => $totalMeetingsInPool,
                    'regular_meetings' => $regularMeetingsCount,
                    'trial_count' => $trialCount,
                    'students_count' => $totalStudentsInPool,
                    'coach_fee' => $coachFee,
                    'salary' => $salary,
                    'sessions' => $allSessions,
                ];

                $totalMeetings += $totalMeetingsInPool;
                $totalStudents += $totalStudentsInPool;
                $totalSalary += $salary;
            }
        }
        
        // Cek kasbon/hutang pelatih
        $coachDebt = 0;
        if ($selectedCoachId) {
            $coach = User::with('wallet')->find($selectedCoachId);
            if ($coach && $coach->wallet && $coach->wallet->balance < 0) {
                $coachDebt = abs($coach->wallet->balance);
            }
        }

        return view('finance.payroll.index', compact('coaches', 'startDate', 'endDate', 'selectedCoachId', 'payrollData', 'totalSalary', 'totalMeetings', 'totalStudents', 'coachDebt'));
    }

    public function paySalary(Request $request)
    {
        $validated = $request->validate([
            'coach_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'amount' => 'required|numeric|min:1',
            'deduction_amount' => 'nullable|string',
            'proof_file' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'notes' => 'nullable|string',
        ]);

        $coach = User::with('wallet')->findOrFail($validated['coach_id']);
        
        $deductionAmount = 0;
        if ($request->filled('deduction_amount')) {
            $deductionAmount = (float) str_replace('.', '', $request->deduction_amount);
        }

        $path = null;
        if ($request->hasFile('proof_file')) {
            $path = $request->file('proof_file')->store('expenses', 'public');
        }

        $reports = TrainingReport::with([
            'schedule.poolLocation',
            'studentAttendances' => function ($q) {
                $q->where('status', 'Hadir');
            }
        ])
            ->where('coach_id', $coach->id)
            ->where('coach_attendance', 'Hadir')
            ->whereBetween('training_date', [$validated['start_date'], $validated['end_date']])
            ->get();

        $trials = Trial::with('poolLocation')
            ->where('coach_id', $coach->id)
            ->where('status', 'hadir')
            ->whereBetween('schedule_date', [$validated['start_date'], $validated['end_date']])
            ->get();

        $reportsByPool = $reports->groupBy(function ($report) {
            return $report->schedule && $report->schedule->poolLocation 
                ? $report->schedule->poolLocation->id 
                : 0;
        });

        $trialsByPool = $trials->groupBy(function ($trial) {
            return $trial->pool_location_id ?? 0;
        });

        $allPoolIds = $reportsByPool->keys()->merge($trialsByPool->keys())->unique();

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            // Proses potongan gaji jika ada
            if ($deductionAmount > 0 && $coach->wallet) {
                // Catat mutasi wallet sebagai pembayaran kasbon
                $coach->wallet->transactions()->create([
                    'type' => 'repay',
                    'amount' => $deductionAmount,
                    'description' => 'Potong gaji otomatis untuk bayar kasbon (Periode: ' . $validated['start_date'] . ' s/d ' . $validated['end_date'] . ')',
                ]);
                $coach->wallet->balance += $deductionAmount;
                $coach->wallet->save();
            }

            $remainingDeduction = $deductionAmount;

            foreach ($allPoolIds as $poolId) {
                $poolReports = $reportsByPool->get($poolId, collect());
                $poolTrials = $trialsByPool->get($poolId, collect());

                $poolLocation = null;
                if ($poolReports->isNotEmpty() && $poolReports->first()->schedule) {
                    $poolLocation = $poolReports->first()->schedule->poolLocation;
                } elseif ($poolTrials->isNotEmpty()) {
                    $poolLocation = $poolTrials->first()->poolLocation;
                }

                $coachFee = $poolLocation->coach_fee ?? 0;

                $regularMeetingsCount = $poolReports->count();
                $regularStudentsCount = $poolReports->sum(function ($report) {
                    return $report->studentAttendances->where('status', 'Hadir')->count();
                });

                $trialCount = $poolTrials->count();

                $totalMeetingsInPool = $regularMeetingsCount + $trialCount;
                $totalStudentsInPool = $regularStudentsCount + $trialCount;
                $salary = $totalStudentsInPool * $coachFee;

                if ($salary > 0 && $poolId > 0) {
                    // Terapkan potongan kasbon ke pengeluaran operasional secara proporsional atau habiskan di pool pertama
                    $poolDeduction = min($salary, $remainingDeduction);
                    $actualExpense = $salary - $poolDeduction;
                    $remainingDeduction -= $poolDeduction;

                    $sesiDetail = "{$totalMeetingsInPool} sesi";
                    if ($trialCount > 0) {
                        $sesiDetail .= " (termasuk {$trialCount} trial)";
                    }

                    $description = "Penggajian \"{$coach->name}\" ({$sesiDetail}, {$totalStudentsInPool} anak, Periode: {$validated['start_date']} s/d {$validated['end_date']})";
                    if ($poolDeduction > 0) {
                        $description .= " (Dipotong hutang/kasbon: Rp " . number_format($poolDeduction, 0, ',', '.') . ")";
                    }
                    if ($validated['notes']) {
                        $description .= " - Catatan: " . $validated['notes'];
                    }

                    \App\Models\OperationalExpense::create([
                        'pool_location_id' => $poolId,
                        'keyword' => 'gaji',
                        'description' => $description,
                        'amount' => $actualExpense,
                        'expense_date' => now()->format('Y-m-d'),
                        'proof_file' => $path,
                    ]);
                }
            }

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('finance.payroll.index', [
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'coach_id' => $validated['coach_id']
            ])->with('success', 'Pembayaran gaji berhasil dicatat sebagai pengeluaran operasional.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat memproses penggajian: ' . $e->getMessage());
        }
    }
}
