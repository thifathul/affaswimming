<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'manual_student_name',
        'pool_location_id',
        'package_type',
        'class_type',
        'amount',
        'credit',
        'practice_start_date',
        'proof_of_payment',
        'status',
        'payment_method',
        'coach_salary_cut',
        'pool_ticket_cut',
        'cash_cut',
        'profit_cut',
        'notes',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function poolLocation()
    {
        return $this->belongsTo(PoolLocation::class);
    }
}
