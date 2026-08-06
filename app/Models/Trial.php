<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trial extends Model
{
    protected $fillable = [
        'name',
        'age',
        'gender',
        'school',
        'contact_number',
        'pool_location_id',
        'coach_id',
        'schedule_date',
        'schedule_time',
        'status',
        'report_note',
        'transaction_id',
    ];

    protected $casts = [
        'schedule_date' => 'date',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function coach()
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    public function poolLocation()
    {
        return $this->belongsTo(PoolLocation::class);
    }
}
