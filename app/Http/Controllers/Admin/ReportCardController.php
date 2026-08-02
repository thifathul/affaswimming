<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\StudentAttendance;

class ReportCardController extends Controller
{
    public function index(Request $request)
    {
        // View all students
        $query = Student::with(['user', 'swimClasses']);

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($u) use ($search) {
                    $u->where('name', 'like', '%' . $search . '%');
                })->orWhere('parent_name', 'like', '%' . $search . '%');
            });
        }

        $students = $query->paginate(15);

        return view('admin.report-cards.index', compact('students'));
    }

    public function show(Student $student)
    {
        $evaluations = StudentAttendance::with(['trainingReport.coach', 'trainingReport.schedule.poolLocation'])
            ->where('student_id', $student->id)
            ->whereNotNull('evaluation')
            ->where('evaluation', '!=', '')
            ->join('training_reports', 'student_attendances.training_report_id', '=', 'training_reports.id')
            ->orderBy('training_reports.training_date', 'desc')
            ->orderBy('student_attendances.created_at', 'desc')
            ->select('student_attendances.*')
            ->get();

        $totalTrainings = StudentAttendance::where('student_id', $student->id)
            ->where('status', 'Hadir')
            ->count();

        return view('admin.report-cards.show', compact('student', 'evaluations', 'totalTrainings'));
    }

    public function updateAdminNote(Request $request, Student $student, StudentAttendance $attendance)
    {
        if ($attendance->student_id !== $student->id) {
            abort(403, 'Invalid attendance record for this student.');
        }

        $request->validate([
            'admin_note' => 'nullable|string'
        ]);

        $attendance->update([
            'admin_note' => $request->admin_note
        ]);

        return back()->with('success', 'Catatan Admin berhasil diperbarui.');
    }
}
