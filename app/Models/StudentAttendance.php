<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAttendance extends Model
{
    protected $fillable = [
        'training_report_id',
        'student_id',
        'status',
        'evaluation',
        'admin_note',
    ];

    public function trainingReport()
    {
        return $this->belongsTo(TrainingReport::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function getMeetingNumberAttribute()
    {
        if (!$this->trainingReport || $this->status !== 'Hadir') {
            return 0;
        }

        // Dapatkan semua kehadiran murid ini yang berstatus 'Hadir'
        // diurutkan berdasarkan tanggal latihan, kemudian berdasarkan ID
        $attendances = self::where('student_id', $this->student_id)
            ->where('status', 'Hadir')
            ->join('training_reports', 'student_attendances.training_report_id', '=', 'training_reports.id')
            ->orderBy('training_reports.training_date', 'asc')
            ->orderBy('student_attendances.id', 'asc')
            ->select('student_attendances.id')
            ->pluck('id');

        // Cari posisi ID kehadiran saat ini di dalam koleksi tersebut (0-indexed)
        $position = $attendances->search($this->id);

        return $position !== false ? $position + 1 : 0;
    }
}
