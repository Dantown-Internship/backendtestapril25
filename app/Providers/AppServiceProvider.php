<?php

namespace App\Providers;

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
        //
    }
}
