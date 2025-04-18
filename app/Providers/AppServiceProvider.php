<?php

namespace App\Providers;


use Illuminate\Support\ServiceProvider;
use App\Models\Expense;
use App\Observers\ExpenseObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
       
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Expense::observe(ExpenseObserver::class);
    }
}
