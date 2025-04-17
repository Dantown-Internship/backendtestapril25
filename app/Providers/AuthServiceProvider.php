<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\Enums\UserRoleEnum;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::define('manage-users',  function(User $user){
            return UserRoleEnum::from($user->role) === UserRoleEnum::Admin;
        });

        Gate::define('manage-expenses', function(User $user){
            return in_array(UserRoleEnum::from($user->role), [UserRoleEnum::Admin, UserRoleEnum::Manager]);
        });

        Gate::define('view-expenses', function(User $user){
            return in_array(UserRoleEnum::from($user->role), [UserRoleEnum::Employee, UserRoleEnum::Admin, UserRoleEnum::Manager]);
        });

        Gate::define('create-expenses', function(User $user) {
            return in_array(UserRoleEnum::from($user->role), [UserRoleEnum::Employee, UserRoleEnum::Admin, UserRoleEnum::Manager]);
        });

        Gate::define('delete-expenses', function(User $user){
            return UserRoleEnum::from($user->role) === UserRoleEnum::Admin;
        });
    }
}
