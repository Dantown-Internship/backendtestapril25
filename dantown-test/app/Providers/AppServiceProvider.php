<?php

namespace App\Providers;

use App\Services\CompanyService;
use App\Services\UserService;
use App\Services\ExpenseService;

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

        // Register ExpenseService
        $this->app->singleton(ExpenseService::class, function () {
            return new ExpenseService();
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
