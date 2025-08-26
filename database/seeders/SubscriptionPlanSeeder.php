<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            // --- FREE PLAN ---
            [
                'name' => 'Free',
                'code' => 'free', // FIX: Added a unique code
                'plan_group' => 'free',
                'price' => 0.00,
                'base_monthly_price' => 0.00,
                'duration_days' => 99999,
                'properties_limit' => 1,
                'rooms_limit' => 3,
                'is_featured' => false,
                'features' => json_encode([
                    'Manage up to 3 rooms',
                    'Standard Invoicing',
                    'Community Support',
                ]),
            ],

            // --- PRO PLANS ---
            [
                'name' => 'Pro Monthly',
                'code' => 'pro-monthly', // FIX: Added a unique code
                'plan_group' => 'pro',
                'price' => 9.99,
                'base_monthly_price' => 9.99,
                'duration_days' => 30,
                'properties_limit' => 5,
                'rooms_limit' => 100,
                'is_featured' => true,
                'features' => json_encode([
                    'Manage up to 100 rooms',
                    'Advanced Invoicing & Utilities',
                    'Tenant Communication Tools',
                    'Email Support',
                ]),
            ],
            [
                'name' => 'Pro Annual',
                'code' => 'pro-annual', // FIX: Added a unique code
                'plan_group' => 'pro',
                'price' => 59.88,
                'base_monthly_price' => 9.99,
                'duration_days' => 365,
                'properties_limit' => 5,
                'rooms_limit' => 100,
                'is_featured' => true,
                'features' => json_encode([
                    'Manage up to 100 rooms',
                    'Advanced Invoicing & Utilities',
                    'Tenant Communication Tools',
                    'Email Support',
                ]),
            ],

            // --- UNLIMITED PLANS ---
            [
                'name' => 'Unlimited Monthly',
                'code' => 'unlimited-monthly', // FIX: Added a unique code
                'plan_group' => 'unlimited',
                'price' => 12.99,
                'base_monthly_price' => 12.99,
                'duration_days' => 30,
                'properties_limit' => 999,
                'rooms_limit' => 9999,
                'is_featured' => false,
                'features' => json_encode([
                    'Unlimited Properties & Rooms',
                    'All Pro Features',
                    'Advanced Analytics',
                    'Priority Phone Support',
                ]),
            ],
            [
                'name' => 'Unlimited Annual',
                'code' => 'unlimited-annual', // FIX: Added a unique code
                'plan_group' => 'unlimited',
                'price' => 119.88,
                'base_monthly_price' => 12.99,
                'duration_days' => 365,
                'properties_limit' => 999,
                'rooms_limit' => 9999,
                'is_featured' => false,
                'features' => json_encode([
                    'Unlimited Properties & Rooms',
                    'All Pro Features',
                    'Advanced Analytics',
                    'Priority Phone Support',
                ]),
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(
                ['name' => $plan['name']],
                $plan
            );
        }
    }
}