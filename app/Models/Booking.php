<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'villa_id',
        'customer_first_name',
        'customer_last_name',
        'customer_email',
        'customer_phone',
        'check_in',
        'check_out',
        'total_price',
        'status',
        'special_requests',
    ];

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'total_price' => 'decimal:2',
    ];

    public function villa()
    {
        return $this->belongsTo(Villa::class);
    }
}
