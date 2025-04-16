<?php

namespace App\Policies;

use App\Models\Expense;
use App\Models\User;

class ExpensePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [User::ROLE_ADMIN, User::ROLE_MANAGER]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Expense $expense): bool
    {
        return $this->canManageExpenses($user, $expense) || $this->belongsToEmployee($user, $expense);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->canCreateExpenses($user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Expense $expense): bool
    {
        return $this->canManageExpenses($user, $expense);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Expense $expense): bool
    {
        return $this->canManageExpenses($user, $expense);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Expense $expense): bool
    {
        return $this->canManageExpenses($user, $expense);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Expense $expense): bool
    {
        return $this->canManageExpenses($user, $expense);
    }


    private function belongsToEmployee(User $user, Expense $expense): bool
    {
        return $user->isEmployee() && $expense->user_id === $user->id;
    }

    private function canCreateExpenses(User $user): bool
    {
        return in_array($user->role, [User::ROLE_ADMIN, User::ROLE_MANAGER, User::ROLE_EMPLOYEE]);
    }

    private function canManageExpenses(User $user, Expense $expense): bool
    {
        return in_array($user->role, [User::ROLE_ADMIN, User::ROLE_MANAGER]) && $user->company_id === $expense->company_id;
    }
}
