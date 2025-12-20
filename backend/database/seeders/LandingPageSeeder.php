<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LandingPageSettings;
use App\Models\LandingService;
use App\Models\LandingReview;

class LandingPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create landing page settings
        LandingPageSettings::create([
            'company_name' => 'MOOWEE',
            'tagline' => 'More than a moving company — we\'re your moving partner',
            'description' => 'Moowee is your trusted partner for stress-free moves.<br>We specialize in residential, commercial, and local moving services, delivering reliability, care, and professionalism with every box we lift. Whether you\'re relocating your home, office, or apartment, Moowee gets you there — smoothly, safely, and on time.',
            'phone' => '+1(310) 753-42-48',
            'email' => 'mooweemoving@gmail.com',
        ]);

        // Create sample reviews
        LandingReview::create([
            'customer_name' => 'Customer 1',
            'review_text' => 'Great service! Highly recommend.',
            'rating' => 5,
            'order' => 1,
            'is_active' => true,
        ]);

        LandingReview::create([
            'customer_name' => 'Customer 2',
            'review_text' => 'Professional and efficient!',
            'rating' => 5,
            'order' => 2,
            'is_active' => true,
        ]);

        LandingReview::create([
            'customer_name' => 'Customer 3',
            'review_text' => 'Best moving experience ever!',
            'rating' => 5,
            'order' => 3,
            'is_active' => true,
        ]);

        echo "Landing page data seeded successfully!\n";
    }
}
