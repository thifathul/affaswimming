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
        $query = ScheduleRequest::with(['schedule.coach', 'substituteCoach', 'proposedPoolLocation', 'schedule.poolLocation']);

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

        $query = TrainingReport::with(['coach', 'schedule.coach', 'schedule.poolLocation', 'studentAttendances.student', 'schedule.scheduleRequests.proposedPoolLocation'])
            ->orderBy('training_date', 'desc')
            ->orderBy('created_at', 'desc');

        if ($date) {
            $query->whereDate('training_date', $date);
        }

        $reports = $query->get();

        return view('admin.operations.recap', compact('reports', 'date'));
    }

    public function createManualRecap(Request $request)
    {
        $schedules = \App\Models\Schedule::with(['coach', 'poolLocation'])->orderBy('day')->orderBy('start_time')->get();
        $selectedSchedule = null;
        if ($request->has('schedule_id')) {
            $selectedSchedule = \App\Models\Schedule::with('students.user')->find($request->schedule_id);
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
