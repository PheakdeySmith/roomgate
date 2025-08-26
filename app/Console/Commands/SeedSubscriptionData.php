<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\SubscriptionPlanSeeder;
use Database\Seeders\UserSubscriptionSeeder;

class SeedSubscriptionData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:seed-subscription-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed subscription plans and user subscriptions with example data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Seeding subscription plans...');
        $this->call('db:seed', [
            '--class' => SubscriptionPlanSeeder::class,
            '--force' => true,
        ]);
        
        $this->info('Seeding user subscriptions...');
        $this->call('db:seed', [
            '--class' => UserSubscriptionSeeder::class,
            '--force' => true,
        ]);
        
        $this->info('Subscription data seeded successfully!');
    }
}
