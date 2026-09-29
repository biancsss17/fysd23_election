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
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Render terminates TLS before forwarding requests to Apache/PHP. Force
        // generated asset, form, and route URLs to remain HTTPS in production.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
