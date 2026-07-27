<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\ScheduleRequest;
use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleDeletionController extends Controller
{
    public function index()
    {
        $requests = ScheduleRequest::with(['schedule.coach', 'schedule.poolLocation', 'schedule.students'])
            ->where('type', 'delete')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('master.schedule_deletions.index', compact('requests'));
    }

    public function approve(Request $request, ScheduleRequest $scheduleRequest)
    {
        if ($scheduleRequest->type !== 'delete' || $scheduleRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'Permintaan tidak valid atau sudah diproses.');
        }

        $schedule = $scheduleRequest->schedule;
        
        if ($schedule) {
            $schedule->delete();
        }

        $scheduleRequest->update([
            'status' => 'approved',
            'admin_note' => $request->admin_note,
        ]);

        return redirect()->back()->with('success', 'Permintaan hapus jadwal disetujui, dan jadwal telah dihapus.');
    }

    public function reject(Request $request, ScheduleRequest $scheduleRequest)
    {
        if ($scheduleRequest->type !== 'delete' || $scheduleRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'Permintaan tidak valid atau sudah diproses.');
        }

        $scheduleRequest->update([
            'status' => 'rejected',
            'admin_note' => $request->admin_note,
        ]);

        return redirect()->back()->with('success', 'Permintaan hapus jadwal ditolak.');
    }
}
