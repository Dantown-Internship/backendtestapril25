<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Expense;
use App\Policies\UserPolicy;
use App\Policies\ExpensePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Resources\Json\JsonResource;

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
        // POLICIES FOR ACTION RESTRICTIONS
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Expense::class, ExpensePolicy::class);

        JsonResource::withoutWrapping();
    }
}
