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
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // Users can only view users within their own company
        return $user->isAdmin() && $user->company_id === $model->company_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Admins can only update users within their own company
        return $user->isAdmin() && $user->company_id === $model->company_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Admins can only delete users within their own company
        // Prevent admins from deleting themselves
        return $user->isAdmin() &&
            $user->company_id === $model->company_id &&
            $user->id !== $model->id;
    }

}
