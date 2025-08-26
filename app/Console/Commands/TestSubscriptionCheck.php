<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestSubscriptionCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-subscription-middleware';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test if the subscription middleware is correctly registered';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking middleware registration...');
        
        // Check if the middleware is registered in the Kernel web middleware group
        $kernelPath = app_path('Http/Kernel.php');
        $kernelContent = file_get_contents($kernelPath);
        
        if (strpos($kernelContent, '\App\Http\Middleware\CheckSubscriptionStatus::class') !== false) {
            $this->info('✓ Middleware is registered in Kernel.php web middleware group');
        } else {
            $this->error('✗ Middleware is not registered in Kernel.php web middleware group');
        }
        
        // Check if the middleware class exists
        if (class_exists(\App\Http\Middleware\CheckSubscriptionStatus::class)) {
            $this->info('✓ CheckSubscriptionStatus middleware class exists');
        } else {
            $this->error('✗ CheckSubscriptionStatus middleware class does not exist');
        }
        
        $this->info('Test completed!');
    }
}
