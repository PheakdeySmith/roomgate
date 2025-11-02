<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            // UserSeeder::class, // Commented out as ComprehensiveSeeder creates users
            // SubscriptionPlanSeeder::class, // Commented out as ComprehensiveSeeder creates plans
            PageContentSeeder::class,
            ComprehensiveSeeder::class, // New comprehensive seeder with all data
        ]);
    }
}