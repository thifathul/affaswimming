<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\PoolLocation;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\CoachAvailability::with(['coach', 'schedules.poolLocation', 'schedules.students']);

        if ($request->filled('coach_id')) {
            $query->where('user_id', $request->coach_id);
        }

        if ($request->filled('day')) {
            $query->where('day', $request->day);
        }

        if ($request->filled('pool_location_id')) {
            $selectedLocation = \App\Models\PoolLocation::find($request->pool_location_id);
            if ($selectedLocation) {
                $locationIds = \App\Models\PoolLocation::where('name', $selectedLocation->name)->pluck('id');
                $query->whereHas('schedules', function ($q) use ($locationIds) {
                    $q->whereIn('pool_location_id', $locationIds);
                });
            }
        }

        $availabilities = $query->orderByRaw("CASE day 
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
            
        $groupedSchedules = collect();
        
        foreach ($availabilities as $availability) {
            $key = $availability->user_id . '-' . $availability->day;
            
            $locations = collect();
            foreach ($availability->schedules as $sch) {
                if ($sch->students->count() > 0 && $sch->poolLocation) {
                    $locations->push($sch->poolLocation->name);
                }
            }

            if (!$groupedSchedules->has($key)) {
                $groupedSchedules->put($key, [
                    'coach_id' => $availability->user_id,
                    'coach_name' => $availability->coach->name ?? 'N/A',
                    'day' => $availability->day,
                    'count' => $availability->schedules->count(),
                    'booked_count' => $availability->schedules->count(),
                    'availabilities' => collect([$availability]),
                    'locations' => $locations->unique()->values()
                ]);
            } else {
                $item = $groupedSchedules->get($key);
                $item['count'] += $availability->schedules->count();
                $item['booked_count'] += $availability->schedules->count();
                $item['availabilities']->push($availability);
                $item['locations'] = collect($item['locations'])->merge($locations)->unique()->values();
                $groupedSchedules->put($key, $item);
            }
        }

        $coaches = \App\Models\User::where('role', 'pelatih')->get();
        $poolLocations = \App\Models\PoolLocation::orderBy('name')->get()->unique('name');

        return view('admin.schedules.index', [
            'groupedSchedules' => $groupedSchedules->sortBy('coach_name')->values(),
            'coaches' => $coaches,
            'poolLocations' => $poolLocations
        ]);
    }

    public function showDay(\App\Models\User $coach, $day)
    {
        $availabilities = \App\Models\CoachAvailability::where('user_id', $coach->id)
            ->where('day', $day)
            ->orderBy('start_time')
            ->get();

        $schedules = Schedule::where('user_id', $coach->id)
            ->where('day', $day)
            ->with(['students', 'poolLocation', 'coachAvailability'])
            ->orderBy('start_time')
            ->get();
            
        $students = Student::where('status', 'aktif')->get();
        $poolLocations = PoolLocation::orderBy('name')->get()->unique('name');
        
        return view('admin.schedules.show', compact('coach', 'day', 'availabilities', 'schedules', 'students', 'poolLocations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'coach_id' => 'required|exists:users,id',
            'day' => 'required|string',
            'coach_availability_id' => 'required|exists:coach_availabilities,id',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'pool_location_id' => 'required|exists:pool_locations,id',
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
        ]);

        $availability = \App\Models\CoachAvailability::findOrFail($validated['coach_availability_id']);

        $availStart = \Carbon\Carbon::parse($availability->start_time)->format('H:i');
        $availEnd = \Carbon\Carbon::parse($availability->end_time)->format('H:i');

        // 1. Boundary Check
        if ($validated['start_time'] < $availStart || $validated['end_time'] > $availEnd) {
            return back()->with('error', 'Jam kelas harus berada di dalam rentang ketersediaan pelatih ('.$availStart.' - '.$availEnd.').');
        }

        // Check gap from availability start time
        $availStartCarbon = \Carbon\Carbon::parse($availability->start_time);
        $newStartCarbon = \Carbon\Carbon::parse($validated['start_time']);
        $diffFromAvailStart = abs($availStartCarbon->diffInMinutes($newStartCarbon));
        if ($diffFromAvailStart > 0 && $diffFromAvailStart < 15) {
            return back()->with('error', 'Jeda dari jam mulai ketersediaan pelatih harus tepat 0 menit atau minimal 15 menit. (Tersedia mulai '.$availStart.')');
        }

        // 2. Gap and Overlap Check
        $existingSchedules = Schedule::where('user_id', $validated['coach_id'])
            ->where('day', $validated['day'])
            ->with('poolLocation')
            ->orderBy('start_time')
            ->get();

        $newStart = \Carbon\Carbon::parse($validated['start_time']);
        $newEnd = \Carbon\Carbon::parse($validated['end_time']);
        $newLocationName = \App\Models\PoolLocation::find($validated['pool_location_id'])->name;

        foreach ($existingSchedules as $existing) {
            $exStart = \Carbon\Carbon::parse($existing->start_time);
            $exEnd = \Carbon\Carbon::parse($existing->end_time);

            // Check Overlap
            if ($newStart < $exEnd && $newEnd > $exStart) {
                return back()->with('error', 'Jadwal bertabrakan dengan jadwal yang sudah ada ('.$existing->start_time.' - '.$existing->end_time.').');
            }

            // Check 15-minute gap if not overlapping, ONLY IF LOCATION IS DIFFERENT (By Name)
            $existingLocName = $existing->poolLocation ? $existing->poolLocation->name : '';
            if ($existingLocName != $newLocationName) {
                if ($newStart >= $exEnd) {
                    // New schedule is AFTER existing
                    if (abs($newStart->diffInMinutes($exEnd)) < 15) {
                        return back()->with('error', 'Jadwal terlalu dekat dengan sesi sebelumnya di lokasi yang berbeda. Minimal jeda mobilitas adalah 15 menit.');
                    }
                } else if ($newEnd <= $exStart) {
                    // New schedule is BEFORE existing
                    if (abs($exStart->diffInMinutes($newEnd)) < 15) {
                        return back()->with('error', 'Jadwal terlalu dekat dengan sesi selanjutnya di lokasi yang berbeda. Minimal jeda mobilitas adalah 15 menit.');
                    }
                }
            }
        }

        // 3. Create Schedule
        $schedule = Schedule::create([
            'user_id' => $validated['coach_id'],
            'coach_availability_id' => $availability->id,
            'day' => $validated['day'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'pool_location_id' => $validated['pool_location_id'],
            'status' => 'booked',
        ]);

        $schedule->students()->attach($validated['student_ids']);

        return back()->with('success', 'Sesi kelas berhasil ditambahkan.');
    }

    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'pool_location_id' => 'nullable|exists:pool_locations,id',
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'exists:students,id',
        ]);
        
        $availability = $schedule->coachAvailability;
        if ($availability) {
            $availStart = \Carbon\Carbon::parse($availability->start_time)->format('H:i');
            $availEnd = \Carbon\Carbon::parse($availability->end_time)->format('H:i');

            // 1. Boundary Check
            if ($validated['start_time'] < $availStart || $validated['end_time'] > $availEnd) {
                return back()->with('error', 'Jam kelas harus berada di dalam rentang ketersediaan pelatih ('.$availStart.' - '.$availEnd.').');
            }

            // Check gap from availability start time
            $availStartCarbon = \Carbon\Carbon::parse($availability->start_time);
            $newStartCarbon = \Carbon\Carbon::parse($validated['start_time']);
            $diffFromAvailStart = abs($availStartCarbon->diffInMinutes($newStartCarbon));
            if ($diffFromAvailStart > 0 && $diffFromAvailStart < 15) {
                return back()->with('error', 'Jeda dari jam mulai ketersediaan pelatih harus tepat 0 menit atau minimal 15 menit. (Tersedia mulai '.$availStart.')');
            }

            // 2. Overlap & Gap Check
            $existingSchedules = Schedule::where('coach_availability_id', $availability->id)
                ->where('id', '!=', $schedule->id)
                ->with('poolLocation')
                ->get();

            $newStart = \Carbon\Carbon::parse($validated['start_time']);
            $newEnd = \Carbon\Carbon::parse($validated['end_time']);
            $newLocationName = null;
            if (!empty($validated['pool_location_id'])) {
                $pool = \App\Models\PoolLocation::find($validated['pool_location_id']);
                if ($pool) $newLocationName = $pool->name;
            }

            foreach ($existingSchedules as $existing) {
                $exStart = \Carbon\Carbon::parse($existing->start_time);
                $exEnd = \Carbon\Carbon::parse($existing->end_time);

                // Check Overlap
                if ($newStart < $exEnd && $newEnd > $exStart) {
                    return back()->with('error', 'Jadwal bertabrakan dengan jadwal yang sudah ada ('.$existing->start_time.' - '.$existing->end_time.').');
                }

                // Check 15-minute gap if not overlapping, ONLY IF LOCATION IS DIFFERENT (By Name)
                $existingLocName = $existing->poolLocation ? $existing->poolLocation->name : '';
                if ($newLocationName && $existingLocName && $existingLocName != $newLocationName) {
                    if ($newStart >= $exEnd) {
                        if (abs($newStart->diffInMinutes($exEnd)) < 15) {
                            return back()->with('error', 'Jadwal terlalu dekat dengan sesi sebelumnya di lokasi yang berbeda. Minimal jeda mobilitas adalah 15 menit.');
                        }
                    } else if ($newEnd <= $exStart) {
                        if (abs($exStart->diffInMinutes($newEnd)) < 15) {
                            return back()->with('error', 'Jadwal terlalu dekat dengan sesi selanjutnya di lokasi yang berbeda. Minimal jeda mobilitas adalah 15 menit.');
                        }
                    }
                }
            }
        }
        
        $schedule->update([
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'pool_location_id' => $validated['pool_location_id'],
            'status' => !empty($validated['student_ids']) ? 'booked' : 'available',
        ]);
        
        if (isset($validated['student_ids'])) {
            $schedule->students()->sync($validated['student_ids']);
        } else {
            $schedule->students()->detach();
        }
        
        return redirect()->back()->with('success', 'Detail jadwal berhasil diperbarui.');
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->students()->detach();
        $schedule->delete();

        return redirect()->back()->with('success', 'Sesi kelas berhasil dihapus.');
    }
}
