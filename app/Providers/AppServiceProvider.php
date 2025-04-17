<?php

namespace App\Providers;

use App\Models\Expense;
use App\Observers\ExpenseObserver;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
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

        RateLimiter::for('login', function (Request $request) {
            return [
                // Limit::perMinute(500),
                Limit::perMinute(3)->by($request->input('email')),
            ];
        });
    }
}
