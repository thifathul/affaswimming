<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScheduleRequest;
use App\Models\TrainingReport;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OperationalController extends Controller
{
    public function approvals(Request $request)
    {
        $query = ScheduleRequest::with(['schedule.coach', 'substituteCoach', 'proposedPoolLocation', 'schedule.poolLocation'])
            ->whereIn('type', ['inval', 'reschedule']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->orderByRaw("CASE status WHEN 'pending' THEN 1 WHEN 'approved' THEN 2 WHEN 'rejected' THEN 3 ELSE 4 END")
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.operations.approvals', compact('requests'));
    }

    public function showApproval(ScheduleRequest $scheduleRequest)
    {
        $scheduleRequest->load(['schedule.coach', 'substituteCoach', 'proposedPoolLocation', 'schedule.poolLocation']);
        return view('admin.operations.show-approval', compact('scheduleRequest'));
    }

    public function updateApproval(Request $request, ScheduleRequest $scheduleRequest)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_note' => 'nullable|string',
        ]);

        $scheduleRequest->update([
            'status' => $validated['status'],
            'admin_note' => $validated['admin_note'],
        ]);

        if ($validated['status'] === 'approved' && $scheduleRequest->absent_student_ids) {
            $absentStudentIds = json_decode($scheduleRequest->absent_student_ids, true);
            if (is_array($absentStudentIds) && count($absentStudentIds) > 0) {
                
                // Calculate end_time based on original schedule duration
                $originalStart = \Carbon\Carbon::parse($scheduleRequest->schedule->start_time);
                $originalEnd = \Carbon\Carbon::parse($scheduleRequest->schedule->end_time);
                $durationInMinutes = $originalStart->diffInMinutes($originalEnd);
                
                $proposedStart = \Carbon\Carbon::parse($scheduleRequest->proposed_start_time);
                $proposedEnd = $proposedStart->copy()->addMinutes($durationInMinutes);
                
                $proposedDate = \Carbon\Carbon::parse($scheduleRequest->proposed_date);
                $dayNameInIndonesian = [
                    'Sunday' => 'Minggu',
                    'Monday' => 'Senin',
                    'Tuesday' => 'Selasa',
                    'Wednesday' => 'Rabu',
                    'Thursday' => 'Kamis',
                    'Friday' => 'Jumat',
                    'Saturday' => 'Sabtu',
                ][$proposedDate->format('l')] ?? 'Senin';

                $makeupSchedule = \App\Models\Schedule::create([
                    'user_id' => $scheduleRequest->type === 'inval' ? $scheduleRequest->substitute_coach_id : $scheduleRequest->schedule->user_id,
                    'day' => $dayNameInIndonesian,
                    'start_time' => $proposedStart->format('H:i:s'),
                    'end_time' => $proposedEnd->format('H:i:s'),
                    'pool_location_id' => $scheduleRequest->proposed_pool_location_id ?? $scheduleRequest->schedule->pool_location_id,
                    'status' => 'booked', // Mark as booked since students are attached
                    'is_makeup' => true,
                ]);
                
                $makeupSchedule->students()->attach($absentStudentIds);
            }
        }

        return redirect()->route('admin.operations.approvals')->with('success', 'Status pengajuan berhasil diperbarui.');
    }

    public function destroyApproval(ScheduleRequest $scheduleRequest)
    {
        $scheduleRequest->delete();

        return redirect()->route('admin.operations.approvals')->with('success', 'Pengajuan berhasil dihapus.');
    }

    public function dailyRecap(Request $request)
    {
        $date = $request->get('date');
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));
        $coach_id = $request->get('coach_id');

        $query = TrainingReport::with(['coach', 'schedule.coach', 'schedule.poolLocation', 'studentAttendances.student', 'schedule.scheduleRequests.proposedPoolLocation'])
            ->orderBy('training_date', 'desc')
            ->orderBy('created_at', 'desc');

        if ($date) {
            $query->whereDate('training_date', $date);
        } else {
            if ($month) {
                $query->whereMonth('training_date', $month);
            }
            if ($year) {
                $query->whereYear('training_date', $year);
            }
        }

        if ($coach_id) {
            $query->where(function ($q) use ($coach_id) {
                $q->where('coach_id', $coach_id)
                  ->orWhereHas('schedule', function ($sq) use ($coach_id) {
                      $sq->where('user_id', $coach_id);
                  });
            });
        }

        $reports = $query->paginate(20)->withQueryString();
        $coaches = \App\Models\User::where('role', 'pelatih')->orderBy('name', 'asc')->get();

        return view('admin.operations.recap', compact('reports', 'date', 'month', 'year', 'coach_id', 'coaches'));
    }

    public function createManualRecap(Request $request)
    {
        $schedules = \App\Models\Schedule::select('schedules.*')
            ->join('users', 'schedules.user_id', '=', 'users.id')
            ->with(['coach', 'poolLocation'])
            ->orderBy('users.name', 'asc')
            ->orderByRaw("CASE day 
                WHEN 'Senin' THEN 1 
                WHEN 'Selasa' THEN 2 
                WHEN 'Rabu' THEN 3 
                WHEN 'Kamis' THEN 4 
                WHEN 'Jumat' THEN 5 
                WHEN 'Sabtu' THEN 6 
                WHEN 'Minggu' THEN 7 
                ELSE 8 END")
            ->orderBy('start_time', 'asc')
            ->get();
            
        $selectedSchedule = null;
        if ($request->has('schedule_id')) {
            $selectedSchedule = \App\Models\Schedule::with(['students' => function($q) {
                $q->orderBy('name', 'asc');
            }, 'students.user'])->find($request->schedule_id);
        }
        return view('admin.operations.manual-recap', compact('schedules', 'selectedSchedule', 'request'));
    }

    public function storeManualRecap(Request $request)
    {
        $validated = $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'training_date' => 'required|date',
            'coach_attendance' => 'required|in:Hadir,Tidak Hadir',
            'report_note' => 'nullable|string',
            'student_attendance' => 'array',
            'student_attendance.*' => 'in:Hadir,Tidak Hadir',
            'student_evaluations' => 'nullable|array',
            'student_evaluations.*' => 'nullable|string',
        ]);

        $schedule = \App\Models\Schedule::findOrFail($validated['schedule_id']);
        $trainingDate = Carbon::parse($validated['training_date']);

        if (TrainingReport::where('schedule_id', $schedule->id)
            ->whereDate('training_date', $trainingDate->format('Y-m-d'))
            ->exists()) {
            return back()->withErrors(['training_date' => 'Laporan presensi untuk jadwal dan tanggal ini sudah pernah dibuat.'])->withInput();
        }

        $report = TrainingReport::create([
            'schedule_id' => $schedule->id,
            'coach_id' => $schedule->user_id, // Default to the main coach of the schedule
            'training_date' => $validated['training_date'],
            'meeting_number' => 0,
            'coach_attendance' => $validated['coach_attendance'],
            'report_note' => $validated['report_note'],
        ]);

        if (isset($validated['student_attendance'])) {
            foreach ($validated['student_attendance'] as $studentId => $status) {
                
                // Mencegah bug: Jika pelatih tidak hadir, paksa status murid jadi Tidak Hadir 
                // agar kuota (billing) murid tidak terpotong otomatis
                if ($validated['coach_attendance'] === 'Tidak Hadir') {
                    $status = 'Tidak Hadir';
                }

                \App\Models\StudentAttendance::create([
                    'training_report_id' => $report->id,
                    'student_id' => $studentId,
                    'status' => $status,
                    'evaluation' => $validated['student_evaluations'][$studentId] ?? null,
                ]);

                // Kurangi kuota jika hadir
                if ($status === 'Hadir') {
                    $student = \App\Models\Student::find($studentId);
                    if ($student) {
                        $student->decrement('remaining_meetings');
                    }
                }
            }
        }

        return redirect()->route('admin.operations.recap')->with('success', 'Rekap latihan manual berhasil disimpan.');
    }

    public function editRecap(TrainingReport $trainingReport)
    {
        $trainingReport->load(['schedule.students', 'schedule.poolLocation', 'studentAttendances.student']);
        
        $schedule = $trainingReport->schedule;

        $invalRequest = \App\Models\ScheduleRequest::where('schedule_id', $schedule->id)
            ->where('type', 'inval')
            ->where('status', 'approved')
            ->where('substitute_coach_id', $trainingReport->coach_id)
            ->where('proposed_date', $trainingReport->training_date)
            ->with('proposedPoolLocation')
            ->first();

        $isSubstitute = $invalRequest !== null;

        return view('admin.operations.edit-recap', compact('trainingReport', 'schedule', 'isSubstitute', 'invalRequest'));
    }

    public function updateRecap(Request $request, TrainingReport $trainingReport)
    {
        $validated = $request->validate([
            'training_date' => 'required|date',
            'coach_attendance' => 'required|in:Hadir,Tidak Hadir',
            'report_note' => 'nullable|string',
            'student_attendance' => 'array',
            'student_attendance.*' => 'in:Hadir,Tidak Hadir',
            'student_evaluations' => 'nullable|array',
            'student_evaluations.*' => 'nullable|string',
        ]);

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $trainingReport->update([
                'training_date' => $validated['training_date'],
                'coach_attendance' => $validated['coach_attendance'],
                'report_note' => $validated['report_note'],
            ]);

            if (isset($validated['student_attendance'])) {
                foreach ($validated['student_attendance'] as $studentId => $status) {
                    
                    if ($validated['coach_attendance'] === 'Tidak Hadir') {
                        $status = 'Tidak Hadir';
                    }

                    $attendance = \App\Models\StudentAttendance::where('training_report_id', $trainingReport->id)
                        ->where('student_id', $studentId)
                        ->first();

                    $oldStatus = $attendance ? $attendance->status : null;
                    
                    if ($attendance) {
                        $attendance->update([
                            'status' => $status,
                            'evaluation' => $validated['student_evaluations'][$studentId] ?? null,
                        ]);
                    } else {
                        \App\Models\StudentAttendance::create([
                            'training_report_id' => $trainingReport->id,
                            'student_id' => $studentId,
                            'status' => $status,
                            'evaluation' => $validated['student_evaluations'][$studentId] ?? null,
                        ]);
                    }

                    // Re-calculate remaining_meetings logic if status changed
                    $student = \App\Models\Student::find($studentId);
                    if ($student && $oldStatus !== $status) {
                        // JIKA sebelumnya Tidak Hadir/Kosong dan SEKARANG Hadir => Kurangi Kuota
                        if ($status === 'Hadir' && ($oldStatus === 'Tidak Hadir' || $oldStatus === null)) {
                            $student->decrement('remaining_meetings');
                        } 
                        // JIKA sebelumnya Hadir dan SEKARANG Tidak Hadir => Kembalikan Kuota
                        elseif ($status === 'Tidak Hadir' && $oldStatus === 'Hadir') {
                            $student->increment('remaining_meetings');
                        }
                    }
                }
            }

            \Illuminate\Support\Facades\DB::commit();
            return redirect()->route('admin.operations.recap')->with('success', 'Laporan berhasil diperbarui.');
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan saat mengupdate laporan: ' . $e->getMessage()])->withInput();
        }
    }

    public function showRecap(TrainingReport $trainingReport)
    {
        $trainingReport->load(['coach', 'schedule.coach', 'schedule.poolLocation', 'studentAttendances.student', 'schedule.scheduleRequests.proposedPoolLocation']);
        return view('admin.operations.show-recap', compact('trainingReport'));
    }

    public function destroyRecap(TrainingReport $trainingReport)
    {
        $trainingReport->delete();
        
        return back()->with('success', 'Laporan latihan berhasil dihapus.');
    }
}
