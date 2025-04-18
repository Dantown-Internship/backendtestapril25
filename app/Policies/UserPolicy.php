<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Only admin can view users
        return $user->role === 'Admin';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // Admin can view any user in same company, other users can only view themselves
        if ($user->role === 'Admin') {
            return $user->company_id === $model->company_id;
        }
        
        return $user->id === $model->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only admin can create users
        return $user->role === 'Admin';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Admin can update any user in same company, other users can only update themselves
        if ($user->role === 'Admin') {
            return $user->company_id === $model->company_id;
        }
        
        return $user->id === $model->id && !isset($model->getDirty()['role']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Only admin can delete users and can't delete themselves
        return $user->role === 'Admin' && $user->id !== $model->id && $user->company_id === $model->company_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        // Only admin can restore users
        return $user->role === 'Admin' && $user->company_id === $model->company_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        // Only admin can force delete users and can't delete themselves
        return $user->role === 'Admin' && $user->id !== $model->id && $user->company_id === $model->company_id;
    }
}
