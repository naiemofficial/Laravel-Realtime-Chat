<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register broadcasting routes with custom 'guest' guard
        Broadcast::routes(['middleware' => ['auth:guest']]);

        // Load channel authorization callbacks
        require base_path('routes/channels.php');
    }
}
