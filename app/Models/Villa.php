<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Villa extends Model
{
    protected $appends = [
        'image_url',
    ];

    protected $fillable = [
        'name',
        'description',
        'price_per_night',
        'image',
        'max_guests',
        'bedrooms',
        'bathrooms',
        'area_sqm',
        'amenities',
        'is_featured',
    ];

    protected $casts = [
        'amenities' => 'array',
        'price_per_night' => 'decimal:2',
        'is_featured' => 'boolean',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function availabilities()
    {
        return $this->hasMany(VillaAvailability::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image) {
            return null;
        }

        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }

        return asset('storage/' . ltrim($this->image, '/'));
    }
}
