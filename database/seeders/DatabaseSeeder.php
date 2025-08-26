<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $landlordRole = Role::firstOrCreate(['name' => 'landlord']);
        $tenantRole = Role::firstOrCreate(['name' => 'tenant']);

        // Create admin user if it doesn't exist
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('11111111'),
            ]
        );
        $adminUser->assignRole($adminRole);

        // Create landlord user if it doesn't exist
        $landlordUser = User::firstOrCreate(
            ['email' => 'landlord@gmail.com'],
            [
                'name' => 'Landlord User',
                'password' => bcrypt('11111111'),
            ]
        );
        $landlordUser->assignRole($landlordRole);

        // Create tenant user if it doesn't exist
        $tenantUser = User::firstOrCreate(
            ['email' => 'tenant@gmail.com'],
            [
                'name' => 'Tenant User',
                'password' => bcrypt('11111111'),
            ]
        );
        $tenantUser->assignRole($tenantRole);
        
        // Create additional landlords for subscription testing
        // Only create them if we don't have enough landlords yet
        $existingLandlordCount = User::role('landlord')->count();
        
        if ($existingLandlordCount < 21) { // We want at least 21 landlords (1 main + 20 additional)
            $neededLandlords = 21 - $existingLandlordCount;
            $additionalLandlords = User::factory()->count($neededLandlords)->create();
            foreach ($additionalLandlords as $user) {
                $user->assignRole($landlordRole);
            }
        }
        
        // Run subscription-related seeders
        $this->call([
            SubscriptionPlanSeeder::class,
        ]);
    }
}
