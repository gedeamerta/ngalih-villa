<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VillaAvailability extends Model
{
    protected $fillable = [
        'villa_id',
        'date',
        'is_available',
        'price_override',
    ];

    protected $casts = [
        'date' => 'date',
        'is_available' => 'boolean',
        'price_override' => 'decimal:2',
    ];

    public function villa()
    {
        return $this->belongsTo(Villa::class);
    }
}
