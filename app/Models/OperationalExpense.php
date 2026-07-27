<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationalExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'pool_location_id',
        'keyword',
        'description',
        'amount',
        'expense_date',
        'proof_file',
    ];

    public function poolLocation()
    {
        return $this->belongsTo(PoolLocation::class);
    }
}
