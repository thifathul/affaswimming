<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Evaluation;

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
        $evaluations = Evaluation::with(['coach', 'swimClass'])
            ->where('student_id', $student->id)
            ->orderBy('meeting_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $totalTrainings = \App\Models\StudentAttendance::where('student_id', $student->id)
            ->where('status', 'Hadir')
            ->count();

        return view('admin.report-cards.show', compact('student', 'evaluations', 'totalTrainings'));
    }
}
