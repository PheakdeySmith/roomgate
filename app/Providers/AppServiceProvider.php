<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
{
    $this->app->bind(
        \App\Repositories\Interfaces\ContractInterface::class,
        \App\Repositories\ContractRepository::class
    );

    $this->app->bind(
        \App\Repositories\Interfaces\RoomInterface::class,
        \App\Repositories\RoomRepository::class
    );

    $this->app->bind(
        \App\Repositories\Interfaces\UserInterface::class,
        \App\Repositories\UserRepository::class
    );
}


    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        require_once app_path('Helpers/route.php');
        require_once app_path('Helpers/money.php');
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
