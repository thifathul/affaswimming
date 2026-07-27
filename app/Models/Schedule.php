<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'user_id',
        'coach_availability_id',
        'day',
        'start_time',
        'end_time',
        'pool_location_id',
        'status',
        'is_makeup',
        'original_schedule_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function coach()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function coachAvailability()
    {
        return $this->belongsTo(CoachAvailability::class);
    }

    public function poolLocation()
    {
        return $this->belongsTo(PoolLocation::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'schedule_student', 'schedule_id', 'student_id');
    }

    public function trainingReports()
    {
        return $this->hasMany(TrainingReport::class);
    }

    public function scheduleRequests()
    {
        return $this->hasMany(ScheduleRequest::class);
    }
}
