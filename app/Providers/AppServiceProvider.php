<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Observers\ExpenseObserver;
use App\Models\Expense;

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
    }
}
