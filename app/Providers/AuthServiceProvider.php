<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Expense;
use App\Policies\ExpensePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Expense::class => ExpensePolicy::class,
    ];
    

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('isAdmin', fn($user) => $user->role === 'Admin');
    }
}
