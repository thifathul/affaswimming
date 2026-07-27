<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingReport extends Model
{
    protected $fillable = [
        'schedule_id',
        'coach_id',
        'training_date',
        'meeting_number',
        'coach_attendance',
        'report_note',
    ];

    protected $casts = [
        'training_date' => 'date',
    ];

    public function coach()
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function studentAttendances()
    {
        return $this->hasMany(StudentAttendance::class);
    }
}
