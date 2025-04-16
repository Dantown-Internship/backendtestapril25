<?php

namespace App\Providers;

use App\Services\CompanyService;
use App\Services\UserService;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register CompanyService
        $this->app->singleton(CompanyService::class, function () {
            return new CompanyService();
        });
        
        // Register UserService
        $this->app->singleton(UserService::class, function () {
            return new UserService();
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
