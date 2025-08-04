<?php

namespace App\Providers;

use App\Services\AdminService;
use App\Services\AdminToken;
use App\Services\SecurityService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AdminService::class, function ($app) {
            return new AdminService();
        });

        $this->app->bind(SecurityService::class, function ($app) {
            return new SecurityService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
