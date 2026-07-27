<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleRequest extends Model
{
    protected $fillable = [
        'schedule_id',
        'type',
        'proposed_date',
        'proposed_start_time',
        'substitute_coach_id',
        'proposed_pool_location_id',
        'reason',
        'status',
        'admin_note',
        'absent_student_ids',
    ];

    protected $casts = [
        'proposed_date' => 'date',
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function substituteCoach()
    {
        return $this->belongsTo(User::class, 'substitute_coach_id');
    }

    public function proposedPoolLocation()
    {
        return $this->belongsTo(PoolLocation::class, 'proposed_pool_location_id');
    }
}
