<?php

namespace Database\Seeders;

use App\Models\Villa;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Villa::create([
            'name' => 'Villa De Miel',
            'description' => "Find your rhythm in Berawa's vibrant heart. Villa De Miel sits minutes from Finns Beach Club and Atlas Beach Fest, with Berawa Beach just an eight minute walk.",
            'price_per_night' => 1710433,
            'image' => 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
            'max_guests' => 6,
            'bedrooms' => 3,
            'bathrooms' => 3,
            'area_sqm' => 200,
            'amenities' => ['High speed WiFi', 'Air conditioning', 'Hot water', 'Free parking', 'In-room safe', 'Filtered drinking water', 'TV with streaming'],
            'is_featured' => true,
        ]);

        Villa::create([
            'name' => 'Villa Serenity',
            'description' => "Experience ultimate peace and tranquility at Villa Serenity, a hidden gem nestled in the lush rice fields of Ubud.",
            'price_per_night' => 2500000,
            'image' => 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
            'max_guests' => 4,
            'bedrooms' => 2,
            'bathrooms' => 2,
            'area_sqm' => 150,
            'amenities' => ['Private Pool', 'Rice Field View', 'Breakfast Included', 'Fast WiFi'],
            'is_featured' => true,
        ]);

        Villa::create([
            'name' => 'The Cliffside Retreat',
            'description' => "Breathtaking ocean views and luxurious amenities await at The Cliffside Retreat in Uluwatu.",
            'price_per_night' => 5000000,
            'image' => 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
            'max_guests' => 8,
            'bedrooms' => 4,
            'bathrooms' => 4,
            'area_sqm' => 400,
            'amenities' => ['Ocean View', 'Infinity Pool', 'Private Chef', 'Spa Services'],
            'is_featured' => true,
        ]);
    }
}
