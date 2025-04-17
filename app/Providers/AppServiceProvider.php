<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\UserService;
use App\Services\ExpenseService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Services\Interfaces\ExpenseServiceInterface::class,
            \App\Services\ExpenseService::class
        );
    
        $this->app->bind(
            \App\Services\Interfaces\UserServiceInterface::class,
            \App\Services\UserService::class
        );

        $this->app->bind(
            \App\Services\Interfaces\LoginServiceInterface::class,
            \App\Services\LoginService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
