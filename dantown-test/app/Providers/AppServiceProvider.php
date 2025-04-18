<?php

namespace App\Providers;

use App\Services\CompanyService;
use App\Services\UserService;
use App\Services\ExpenseService;
use App\Jobs\SendExpenseReport;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

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
         $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->job(new SendExpenseReport)->weekly();
        });
    }
}
