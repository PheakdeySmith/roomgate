<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestAuth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-auth';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test user authentication and role detection';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing User Authentication & Role Detection');
        $this->info('===========================================');
        
        // Get all users with roles
        $users = User::with('roles')->get();
        
        $this->table(
            ['ID', 'Name', 'Email', 'Roles', 'isLandlord()'],
            $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->roles->pluck('name')->implode(', '),
                    'isLandlord' => $user->isLandlord() ? 'Yes' : 'No',
                ];
            })
        );
        
        // Check if our CheckSubscriptionStatus middleware would correctly identify landlords
        $landlords = $users->filter(function ($user) {
            return $user->isLandlord();
        });
        
        $this->info("\nLandlord Users who would be checked for subscriptions: " . $landlords->count());
        
        // Test subscription related methods
        $this->info("\nTesting Subscription Methods");
        $this->info('============================');
        
        $landlords->each(function ($landlord) {
            $this->info("\nLandlord: {$landlord->name} ({$landlord->email})");
            $this->info("Has active subscription: " . ($landlord->hasActiveSubscription() ? 'Yes' : 'No'));
            
            $subscription = $landlord->activeSubscription();
            if ($subscription) {
                $this->info("Subscription Plan: {$subscription->subscriptionPlan->name}");
                $this->info("Status: {$subscription->status}");
                $this->info("Days Remaining: {$subscription->days_remaining}");
                $this->info("Payment Status: {$subscription->payment_status}");
                $this->info("Is in Trial: " . ($subscription->isInTrial() ? 'Yes' : 'No'));
            } else {
                $this->info("No active subscription found");
            }
        });
    }
}
