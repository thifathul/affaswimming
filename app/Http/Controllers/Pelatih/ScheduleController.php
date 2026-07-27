<?php

namespace App\Http\Controllers\Pelatih;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $schedules = Schedule::where(function ($q) use ($userId) {
            $q->where('user_id', $userId)
              ->where(function ($q2) {
                  $q2->where('is_makeup', false)
                     ->orWhere(function ($q3) {
                         $q3->whereDoesntHave('trainingReports')
                            ->orWhereHas('trainingReports', function ($q4) {
                                $q4->whereDate('training_date', now()->format('Y-m-d'));
                            });
                     });
              });
        })
        ->orWhereHas('scheduleRequests', function ($query) use ($userId) {
            $query->where('type', 'inval')
                  ->where('status', 'approved')
                  ->where('substitute_coach_id', $userId)
                  ->where('proposed_date', '>=', now()->subDays(7)->format('Y-m-d'));
        })
            ->with(['students', 'poolLocation', 'scheduleRequests' => function ($query) use ($userId) {
                $query->where('type', 'inval')
                      ->where('status', 'approved')
                      ->where('substitute_coach_id', $userId)
                      ->where('proposed_date', '>=', now()->subDays(7)->format('Y-m-d'))
                      ->with('proposedPoolLocation');
            }])
            ->orderByRaw("CASE day 
                WHEN 'Senin' THEN 1 
                WHEN 'Selasa' THEN 2 
                WHEN 'Rabu' THEN 3 
                WHEN 'Kamis' THEN 4 
                WHEN 'Jumat' THEN 5 
                WHEN 'Sabtu' THEN 6 
                WHEN 'Minggu' THEN 7 
                ELSE 8 END")
            ->orderBy('start_time')
            ->get();

        $dayOrder = ['Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6, 'Minggu' => 7];

        foreach ($schedules as $schedule) {
            if ($schedule->user_id !== $userId && $schedule->scheduleRequests->isNotEmpty()) {
                $invalReq = $schedule->scheduleRequests->first();
                if ($invalReq && $invalReq->proposed_date) {
                    $proposedDate = \Carbon\Carbon::parse($invalReq->proposed_date);
                    $dayNameInIndonesian = [
                        'Sunday' => 'Minggu',
                        'Monday' => 'Senin',
                        'Tuesday' => 'Selasa',
                        'Wednesday' => 'Rabu',
                        'Thursday' => 'Kamis',
                        'Friday' => 'Jumat',
                        'Saturday' => 'Sabtu',
                    ][$proposedDate->format('l')] ?? $schedule->day;
                    
                    $schedule->day = $dayNameInIndonesian;
                }
            }
        }

        // Re-sort in memory just in case the day was changed
        $schedules = $schedules->sortBy(function($schedule) use ($dayOrder) {
            return ($dayOrder[$schedule->day] ?? 8) . '-' . $schedule->start_time;
        })->values();

        $reportsToday = \App\Models\TrainingReport::with('studentAttendances')
            ->whereDate('training_date', now()->format('Y-m-d'))
            ->where('coach_id', $userId)
            ->get();

        $submittedScheduleIds = $reportsToday->pluck('schedule_id')->toArray();

        return view('pelatih.schedules.index', compact('schedules', 'submittedScheduleIds', 'reportsToday'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'day' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        \App\Models\CoachAvailability::create([
            'user_id' => Auth::id(),
            'day' => $validated['day'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
        ]);

        return redirect()->route('pelatih.schedules.index')->with('success', 'Jadwal kosong berhasil ditambahkan. Menunggu admin membuat kelas.');
    }

    public function allSchedules(Request $request)
    {
        $userId = Auth::id();

        $query = Schedule::where('user_id', '!=', $userId)->with(['coach', 'students', 'poolLocation']);

        if ($request->filled('coach_id')) {
            $query->where('user_id', $request->coach_id);
        }
        if ($request->filled('day')) {
            $query->where('day', $request->day);
        }
        if ($request->filled('pool_location_id')) {
            $query->where('pool_location_id', $request->pool_location_id);
        }

        $schedules = $query->orderByRaw("CASE day 
                WHEN 'Senin' THEN 1 
                WHEN 'Selasa' THEN 2 
                WHEN 'Rabu' THEN 3 
                WHEN 'Kamis' THEN 4 
                WHEN 'Jumat' THEN 5 
                WHEN 'Sabtu' THEN 6 
                WHEN 'Minggu' THEN 7 
                ELSE 8 END")
            ->orderBy('start_time')
            ->get()
            ->sortBy(function($schedule) { return $schedule->coach->name ?? ''; })
            ->values();

        $coaches = \App\Models\User::where('role', 'pelatih')->where('id', '!=', $userId)->orderBy('name')->get();
        $poolLocations = \App\Models\PoolLocation::orderBy('name')->get();

        return view('pelatih.schedules.all', compact('schedules', 'coaches', 'poolLocations'));
    }

    public function requestDelete(Request $request, Schedule $schedule)
    {
        if ($schedule->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $schedule->scheduleRequests()->create([
            'type' => 'delete',
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Permintaan hapus jadwal berhasil dikirim.');
    }
}
