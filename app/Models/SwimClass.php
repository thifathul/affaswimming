<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SwimClass extends Model
{
    protected $fillable = ['name', 'schedule', 'description', 'status'];

    public function coaches()
    {
        return $this->belongsToMany(User::class, 'coach_swim_class', 'swim_class_id', 'user_id');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_swim_class', 'swim_class_id', 'student_id');
    }
}
