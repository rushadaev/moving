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

        // Create test admin user
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        $user->assignRole('admin');

        // Seed other data
        $this->call([
            RequestSeeder::class,
            PackingMaterialSeeder::class,
            LandingServiceSeeder::class,
        ]);
    }
}
