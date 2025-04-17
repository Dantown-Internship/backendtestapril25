<?php

namespace App\Providers;

use App\Models\Expense;
use App\Models\User;
use App\Observers\ExpenseObserver;
use App\Policies\ExpensePolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
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
        //policies
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Expense::class, ExpensePolicy::class);

        //observer
        Expense::observe(ExpenseObserver::class);

    }
}
