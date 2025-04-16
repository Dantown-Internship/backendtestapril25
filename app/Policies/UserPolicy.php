<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any users.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the user.
     *
     * @param User $user
     * @param User $model
     * @return bool
     */
    public function view(User $user, User $model): bool
    {
        return $user->company_id === $model->company_id;
    }

    /**
     * Determine whether the user can create users.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the user.
     *
     * @param User $user
     * @param User $model
     * @return bool
     */
    public function update(User $user, User $model): bool
    {
        return $user->company_id === $model->company_id &&
               ($user->isAdmin() || $user->id === $model->id);
    }

    /**
     * Determine whether the user can delete the user.
     *
     * @param User $user
     * @param User $model
     * @return bool
     */
    public function delete(User $user, User $model): bool
    {
        return $user->company_id === $model->company_id &&
               $user->isAdmin() &&
               $user->id !== $model->id;
    }

    /**
     * Determine whether the user can update their own password.
     *
     * @param User $user
     * @param User $model
     * @return bool
     */
    public function updatePassword(User $user, User $model): bool
    {
        return $user->id === $model->id;
    }
}
