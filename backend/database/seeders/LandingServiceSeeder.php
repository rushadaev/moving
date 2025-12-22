<?php

namespace Database\Seeders;

use App\Models\LandingService;
use Illuminate\Database\Seeder;

class LandingServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'title' => 'Residential Moving',
                'icon' => 'landing/icons/Resident Moving.svg',
                'description' => 'Professional residential moving services for your home. We handle everything from packing to unpacking, ensuring your belongings are safe and secure.',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Commercial Moving',
                'icon' => 'landing/icons/Commercial Moving.svg',
                'description' => 'Efficient commercial moving solutions for offices and businesses. Minimal downtime, maximum efficiency for your business relocation.',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Specialty Moving Services',
                'icon' => 'landing/icons/Piano.svg',
                'description' => 'Expert handling of specialty items including pianos, gun safes, and other valuable possessions. Our trained professionals ensure delicate items are moved safely.',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'title' => 'Long Distance Moving',
                'icon' => 'landing/icons/Driving.svg',
                'description' => 'Reliable long-distance moving services across states. We coordinate logistics and ensure your items arrive on time and in perfect condition.',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'title' => 'Labor Services',
                'icon' => 'landing/icons/Labor.svg',
                'description' => 'Professional labor services for loading, unloading, and heavy lifting. Flexible hourly rates to fit your specific needs.',
                'order' => 5,
                'is_active' => true,
            ],
            [
                'title' => 'Storage Services',
                'icon' => 'landing/icons/Storaging.svg',
                'description' => 'Secure storage solutions for short-term or long-term needs. Climate-controlled facilities to keep your belongings safe.',
                'order' => 6,
                'is_active' => true,
            ],
            [
                'title' => 'Packing Services',
                'icon' => 'landing/icons/Packing.svg',
                'description' => 'Full-service packing with quality materials. From fragile items to entire households, we pack everything professionally and efficiently.',
                'order' => 7,
                'is_active' => true,
            ],
            [
                'title' => 'Home Maintenance',
                'icon' => 'landing/icons/Smart Home.svg',
                'description' => 'Additional home services including TV installation, smart home setup, and general maintenance. One-stop solution for your moving and setup needs.',
                'order' => 8,
                'is_active' => true,
            ],
            [
                'title' => 'Last Call Moving',
                'icon' => 'landing/icons/Last call.svg',
                'description' => 'Emergency and last-minute moving services. We understand urgent situations and provide quick, reliable moving solutions when you need them most.',
                'order' => 9,
                'is_active' => true,
            ],
        ];

        foreach ($services as $service) {
            LandingService::updateOrCreate(
                ['title' => $service['title']],
                $service
            );
        }

        $this->command->info('Landing services seeded successfully!');
    }
}
