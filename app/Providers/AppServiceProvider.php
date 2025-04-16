<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\UserPolicy;
use App\Policies\ExpensePolicy;
use Illuminate\Support\Facades\Gate;
use App\Http\Middleware\CheckUserRole;
use Illuminate\Support\ServiceProvider;
use App\Http\Middleware\UserIsInCompany;

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
        Gate::define('expense:create', function (User $user) {
            return in_array($user->role, ['Admin', 'Manager', 'Employee']);
        });
        Gate::define('update-expense', [ExpensePolicy::class, 'update']);
        Gate::define('delete-expense', [ExpensePolicy::class, 'delete']);
        Gate::define('update-user', [UserPolicy::class, 'update']);
        Gate::define('delete-user', [UserPolicy::class, 'delete']);
    }
}
