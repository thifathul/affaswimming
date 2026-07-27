<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'name',
        'birth_place_date',
        'age',
        'school',
        'status',
        'user_id',
        'package_active_until',
        'remaining_meetings',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function swimClasses()
    {
        return $this->belongsToMany(SwimClass::class, 'student_swim_class', 'student_id', 'swim_class_id')->withTimestamps();
    }

    public function schedules()
    {
        return $this->belongsToMany(Schedule::class, 'schedule_student', 'student_id', 'schedule_id');
    }

    public function attendances()
    {
        return $this->hasMany(StudentAttendance::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
