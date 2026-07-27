<?php

namespace App\Http\Controllers\Pelatih;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\TrainingReport;
use App\Models\StudentAttendance;
use App\Models\ScheduleRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TrainingReportController extends Controller
{
    public function index()
    {
        $reports = TrainingReport::with(['schedule.poolLocation', 'studentAttendances.student', 'schedule.scheduleRequests.proposedPoolLocation'])
            ->where('coach_id', auth()->id())
            ->orderBy('training_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pelatih.reports.index', compact('reports'));
    }

    public function requestsIndex()
    {
        $requests = ScheduleRequest::with(['schedule.poolLocation', 'proposedPoolLocation', 'substituteCoach'])
            ->whereHas('schedule', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pelatih.requests.index', compact('requests'));
    }

    public function create(Schedule $schedule)
    {
        // Cek apakah user adalah pelatih utama atau pelatih pengganti (inval) yang disetujui dalam 7 hari terakhir
        $invalRequest = ScheduleRequest::where('schedule_id', $schedule->id)
            ->where('type', 'inval')
            ->where('status', 'approved')
            ->where('substitute_coach_id', auth()->id())
            ->where('proposed_date', '>=', Carbon::now()->subDays(7)->format('Y-m-d'))
            ->with('proposedPoolLocation')
            ->first();

        $isSubstitute = $invalRequest !== null;

        if ($schedule->user_id !== auth()->id() && !$isSubstitute) {
            abort(403, 'Anda tidak memiliki akses ke jadwal ini.');
        }

        $defaultDate = $isSubstitute ? $invalRequest->proposed_date->format('Y-m-d') : Carbon::now()->format('Y-m-d');

        return view('pelatih.reports.create', compact('schedule', 'defaultDate', 'isSubstitute', 'invalRequest'));
    }

    public function store(Request $request, Schedule $schedule)
    {
        // Cek apakah user adalah pelatih utama atau pelatih pengganti (inval)
        $isSubstitute = ScheduleRequest::where('schedule_id', $schedule->id)
            ->where('type', 'inval')
            ->where('status', 'approved')
            ->where('substitute_coach_id', auth()->id())
            ->where('proposed_date', '>=', Carbon::now()->subDays(7)->format('Y-m-d'))
            ->exists();

        if ($schedule->user_id !== auth()->id() && !$isSubstitute) {
            abort(403, 'Anda tidak memiliki akses untuk submit laporan ke jadwal ini.');
        }

        $validated = $request->validate([
            'training_date' => 'required|date',
            'coach_attendance' => 'required|in:Hadir,Tidak Hadir',
            'report_note' => 'nullable|string',
            'student_attendance' => 'array',
            'student_attendance.*' => 'in:Hadir,Tidak Hadir',
            'student_evaluations' => 'nullable|array',
            'student_evaluations.*' => 'nullable|string',
        ]);

        $trainingDate = Carbon::parse($validated['training_date']);
        
        // Validasi: tidak boleh submit laporan jika tanggal latihan lebih dari 7 hari yang lalu
        if (now()->diffInDays($trainingDate) > 7 && $trainingDate->isPast()) {
            return back()->withErrors(['training_date' => 'Laporan tidak dapat disubmit karena sudah lebih dari 7 hari dari tanggal latihan.']);
        }

        // Validasi: hanya bisa melakukan presensi/laporan sekali untuk jadwal dan tanggal ini
        if (TrainingReport::where('schedule_id', $schedule->id)
            ->whereDate('training_date', $trainingDate->format('Y-m-d'))
            ->exists()) {
            return back()->withErrors(['training_date' => 'Laporan presensi untuk jadwal dan tanggal ini sudah pernah dibuat.']);
        }

        // Simpan Report
        $report = TrainingReport::create([
            'schedule_id' => $schedule->id,
            'coach_id' => auth()->id(), // Mencatat pelatih aktual yang absen
            'training_date' => $validated['training_date'],
            'meeting_number' => 0, // Not used anymore, replaced by dynamic calculation per student
            'coach_attendance' => $validated['coach_attendance'],
            'report_note' => $validated['report_note'],
        ]);

        // Simpan Kehadiran Murid
        if (isset($validated['student_attendance'])) {
            foreach ($validated['student_attendance'] as $studentId => $status) {
                StudentAttendance::create([
                    'training_report_id' => $report->id,
                    'student_id' => $studentId,
                    'status' => $status,
                    'evaluation' => $validated['student_evaluations'][$studentId] ?? null,
                ]);

                // Kurangi kuota pertemuan jika murid Hadir
                if ($status === 'Hadir') {
                    $student = \App\Models\Student::find($studentId);
                    if ($student) {
                        $student->decrement('remaining_meetings');
                    }
                }
            }
        }

        return redirect()->route('pelatih.schedules.index')->with('success', 'Laporan dan kehadiran berhasil disimpan.');
    }

    public function requestForm(Schedule $schedule)
    {
        if ($schedule->user_id !== auth()->id()) {
            abort(403);
        }
        
        // Ambil data pelatih lain untuk opsi inval
        $coaches = User::where('role', 'pelatih')->where('id', '!=', auth()->id())->get();
        $poolLocations = \App\Models\PoolLocation::all();

        return view('pelatih.requests.create', compact('schedule', 'coaches', 'poolLocations'));
    }

    public function requestAbsentForm(Schedule $schedule)
    {
        if ($schedule->user_id !== auth()->id()) {
            abort(403);
        }

        // Cari laporan hari ini untuk jadwal ini
        $reportToday = TrainingReport::with(['studentAttendances.student'])
            ->where('schedule_id', $schedule->id)
            ->whereDate('training_date', now()->format('Y-m-d'))
            ->first();

        if (!$reportToday) {
            return redirect()->route('pelatih.schedules.index')->with('error', 'Laporan belum dibuat untuk jadwal ini hari ini.');
        }

        $absentAttendances = $reportToday->studentAttendances->where('status', 'Tidak Hadir');
        if ($absentAttendances->isEmpty()) {
            return redirect()->route('pelatih.schedules.index')->with('error', 'Tidak ada murid absen pada jadwal ini hari ini.');
        }

        $absentStudents = $absentAttendances->map(function ($attendance) {
            return $attendance->student;
        });

        // Ambil data pelatih lain untuk opsi inval
        $coaches = User::where('role', 'pelatih')->where('id', '!=', auth()->id())->get();
        $poolLocations = \App\Models\PoolLocation::all();

        return view('pelatih.requests.create-absent', compact('schedule', 'coaches', 'poolLocations', 'absentStudents'));
    }

    public function submitRequest(Request $request, Schedule $schedule)
    {
        if ($schedule->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'type' => 'required|in:reschedule,inval',
            'proposed_date' => 'required|date|after_or_equal:today',
            'proposed_start_time' => 'required|date_format:H:i',
            'substitute_coach_id' => 'nullable|exists:users,id',
            'proposed_pool_location_id' => 'nullable|exists:pool_locations,id',
            'reason' => 'required|string|max:500',
            'absent_student_ids' => 'nullable|array',
            'absent_student_ids.*' => 'exists:students,id',
        ]);

        // Jika inval, substitute_coach_id wajib diisi
        if ($validated['type'] === 'inval' && empty($validated['substitute_coach_id'])) {
            return back()->withErrors(['substitute_coach_id' => 'Pelatih pengganti wajib dipilih untuk pengajuan Inval.'])->withInput();
        }

        ScheduleRequest::create([
            'schedule_id' => $schedule->id,
            'type' => $validated['type'],
            'proposed_date' => $validated['proposed_date'],
            'proposed_start_time' => $validated['proposed_start_time'],
            'substitute_coach_id' => $validated['type'] === 'inval' ? $validated['substitute_coach_id'] : null,
            'proposed_pool_location_id' => $validated['proposed_pool_location_id'],
            'reason' => $validated['reason'],
            'absent_student_ids' => $request->has('absent_student_ids') ? json_encode($validated['absent_student_ids']) : null,
            'status' => 'pending',
        ]);

        return redirect()->route('pelatih.schedules.index')->with('success', 'Pengajuan ' . ucfirst($validated['type']) . ' berhasil dikirim dan menunggu persetujuan.');
    }
}
