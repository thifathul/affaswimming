<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoolLocation extends Model
{
    protected $fillable = [
        'package_name',
        'name',
        'meeting_count',
        'coach_fee',
        'cash_percentage',
        'private_ticket_price',
        'semi_private_ticket_price',
    ];

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
