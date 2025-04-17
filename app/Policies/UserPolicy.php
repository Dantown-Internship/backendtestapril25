<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $admin): bool
    {
        return $admin->role === 'Admin';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $admin, User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $admin): bool
    {
        return $admin->role === 'Admin';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $admin, User $user): bool
    {
        return $admin->role === 'Admin' &&
            $admin->company_id === $user->company_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $admin, User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }
}
