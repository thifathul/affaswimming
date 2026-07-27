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
            ->get()
            ->sortByDesc(function ($att) {
                return $att->trainingReport->training_date;
            });

        return view('student.reports.index', compact('attendances'));
    }
}
