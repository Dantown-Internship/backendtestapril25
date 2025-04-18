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
        // All authenticated users can view expenses
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Expense $expense): bool
    {
        // User can only view expenses from their company
        // Admin and Managers can view all expenses in their company
        // Employees can only view their own expenses
        if ($user->company_id !== $expense->company_id) {
            return false;
        }
        
        if (in_array($user->role, ['Admin', 'Manager'])) {
            return true;
        }
        
        return $user->id === $expense->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // All authenticated users with a company_id can create expenses
        return $user->company_id !== null;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Expense $expense): bool
    {
        // Admin and Managers can update any expense in their company
        // Employees can only update their own expenses
        if ($user->company_id !== $expense->company_id) {
            return false;
        }
        
        if (in_array($user->role, ['Admin', 'Manager'])) {
            return true;
        }
        
        return $user->id === $expense->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Expense $expense): bool
    {
        // Only Admin can delete expenses in their company
        return $user->role === 'Admin' && $user->company_id === $expense->company_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Expense $expense): bool
    {
        // Only Admin can restore expenses in their company
        return $user->role === 'Admin' && $user->company_id === $expense->company_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Expense $expense): bool
    {
        // Only Admin can force delete expenses in their company
        return $user->role === 'Admin' && $user->company_id === $expense->company_id;
    }
}
