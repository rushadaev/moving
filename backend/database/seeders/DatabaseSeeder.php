<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Seed roles first
        $this->call([
            RoleSeeder::class,
        ]);

        // Create test admin user (only if doesn't exist)
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        if (!$user->hasRole('admin')) {
            $user->assignRole('admin');
        }

        // Seed other data
        $this->call([
            RequestSeeder::class,
            PackingMaterialSeeder::class,
            LandingServiceSeeder::class,
        ]);
    }
}
