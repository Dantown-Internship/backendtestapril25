<?php

namespace App\Providers;

use App\Models\Expense;
use App\Models\User;
use App\Observers\ExpenseObserver;
use App\Services\ExpenseReportPdfGenerator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ExpenseReportPdfGenerator::class, function ($app) {
            return new ExpenseReportPdfGenerator();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Expense::observe(ExpenseObserver::class);
    }
}
