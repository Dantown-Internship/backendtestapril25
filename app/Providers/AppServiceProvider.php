<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Models\Expense;
use App\Observers\UserObserver;
use App\Observers\ExpenseObserver;
use App\Services\AuditLogger;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register the AuditLogger service
        $this->app->singleton(AuditLogger::class, function ($app) {
            return new AuditLogger();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register observers
        User::observe(UserObserver::class);
        Expense::observe(ExpenseObserver::class);
    }
}
