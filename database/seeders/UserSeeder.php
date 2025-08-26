<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            ['name' => 'Admin User', 'password' => bcrypt('11111111')]
        );
        $admin->assignRole('admin');

        // Create landlord user
        $landlord = User::firstOrCreate(
            ['email' => 'landlord@gmail.com'],
            ['name' => 'Landlord User', 'password' => bcrypt('11111111')]
        );
        $landlord->assignRole('landlord');

        // Create tenant user
        $tenant = User::firstOrCreate(
            ['email' => 'tenant@gmail.com'],
            ['name' => 'Tenant User', 'password' => bcrypt('11111111')]
        );
        $tenant->assignRole('tenant');

    }
}