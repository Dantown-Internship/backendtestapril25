<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ExpensePolicy
{

    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability, ?Expense $expense): bool|null
    {
        if ($user->company_id != $expense->company_id) {
            return false;
        }

        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Expense $expense): bool
    {
        if ($user->id == $expense->user_id) {
            return true;
        }
        if ($user->role == RoleEnum::ADMIN() || $user->role == RoleEnum::MANAGER()) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Expense $expense): bool
    {
        return $user->role == RoleEnum::ADMIN() || $user->role == RoleEnum::MANAGER();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Expense $expense): bool
    {
        return $user->role == RoleEnum::ADMIN();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Expense $expense): bool
    {
        return $user->role == RoleEnum::ADMIN();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Expense $expense): bool
    {
        return $user->role == RoleEnum::ADMIN();
    }
}
