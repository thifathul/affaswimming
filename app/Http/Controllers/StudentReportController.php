<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentAttendance;

class StudentReportController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if (!$user->student) {
            return redirect()->route('dashboard')->with('error', 'Data murid tidak ditemukan.');
        }

        $attendances = StudentAttendance::with(['trainingReport.coach', 'trainingReport.schedule.poolLocation'])
            ->where('student_id', $user->student->id)
            ->whereHas('trainingReport')
            ->join('training_reports', 'student_attendances.training_report_id', '=', 'training_reports.id')
            ->orderBy('training_reports.training_date', 'asc')
            ->orderBy('student_attendances.created_at', 'asc')
            ->select('student_attendances.*')
            ->get();

        return view('student.reports.index', compact('attendances'));
    }
}
