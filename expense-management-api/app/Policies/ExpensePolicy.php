<?php

namespace App\Policies;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ExpensePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view expenses from their company
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Expense $expense): bool
    {
        // Users can only view expenses from their own company
        return $user->company_id === $expense->company_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // All authenticated users can create expenses
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Expense $expense): bool
    {
        // Only Admins and Managers can update expenses, and only within their company
        return ($user->isAdmin() || $user->isManager()) &&
            $user->company_id === $expense->company_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Expense $expense): bool
    {
        // Only Admins can delete expenses, and only within their company
        return $user->isAdmin() && $user->company_id === $expense->company_id;
    }
}
