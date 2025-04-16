<?php

namespace App\Policies;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExpensePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any expenses.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the expense.
     *
     * @param User $user
     * @param Expense $expense
     * @return bool
     */
    public function view(User $user, Expense $expense): bool
    {
        return $user->company_id === $expense->company_id;
    }

    /**
     * Determine whether the user can create expenses.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the expense.
     *
     * @param User $user
     * @param Expense $expense
     * @return bool
     */
    public function update(User $user, Expense $expense): bool
    {
        return $user->company_id === $expense->company_id &&
               ($user->isAdmin() || $user->id === $expense->user_id);
    }

    /**
     * Determine whether the user can delete the expense.
     *
     * @param User $user
     * @param Expense $expense
     * @return bool
     */
    public function delete(User $user, Expense $expense): bool
    {
        return $user->company_id === $expense->company_id &&
               ($user->isAdmin() || $user->id === $expense->user_id);
    }
}
