<?php

namespace App\Providers;

use App\Auth\EraporUserProvider;
use App\Models\UserLogin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Auth::provider('erapor', function ($app, array $config) {
            return new EraporUserProvider($app['hash'], $config['model']);
        });
    }
}
