<?php

namespace App\Providers;

use App\Models\Expense;
use App\Observers\ExpenseObserver;
use App\Services\AuditLogService;
use App\Services\AuthService;
use App\Services\Contracts\AuditLogServiceInterface;
use App\Services\Contracts\AuthServiceInterface;
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
        Expense::observe(ExpenseObserver::class);
        $this->app->bind(AuthServiceInterface::class, AuthService::class);
        $this->app->bind(AuditLogServiceInterface::class, AuditLogService::class);
    }
}
