<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Request;
use App\Models\User;
use App\Models\Address;
use App\Models\Material;

class RequestSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first() ?? User::factory()->create();

        $request = Request::create([
            'request_number' => 'REQ-' . strtoupper(substr(md5(uniqid()), 0, 8)),
            'property_type' => 'residential',
            'square_feet' => 1000.00,
            'additional_objects' => ['garage', 'storage'],
            'movers_count' => 3,
            'hourly_rate' => 125.00,
            'departure_time' => now()->addDays(2),
            'labor_included' => true,
            'package_type' => 'standard',
            'price' => 150.00,
            'status' => 'pending',
            'user_id' => $user->id,
        ]);

        // Add addresses
        Address::create([
            'request_id' => $request->id,
            'address' => '123 Main St, New York, NY',
            'latitude' => 40.7128,
            'longitude' => -74.0060,
            'order' => 0,
            'type' => 'loading'
        ]);

        Address::create([
            'request_id' => $request->id,
            'address' => '456 Park Ave, New York, NY',
            'latitude' => 40.7527,
            'longitude' => -73.9772,
            'order' => 1,
            'type' => 'intermediate'
        ]);

        Address::create([
            'request_id' => $request->id,
            'address' => '789 Broadway, New York, NY',
            'latitude' => 40.7589,
            'longitude' => -73.9851,
            'order' => 2,
            'type' => 'unloading'
        ]);

        // Add materials
        $materials = [
            ['name' => 'Boxes', 'quantity' => 10],
            ['name' => 'Furniture', 'quantity' => 5],
            ['name' => 'Electronics', 'quantity' => 3]
        ];

        foreach ($materials as $material) {
            Material::create([
                'request_id' => $request->id,
                'name' => $material['name'],
                'quantity' => $material['quantity']
            ]);
        }
    }
} 