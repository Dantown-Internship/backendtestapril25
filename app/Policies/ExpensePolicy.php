<?php

namespace App\Policies;

use App\Models\Expense;
use App\Models\User;

class ExpensePolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Expense $expense)
    {
        return $user->company_id === $expense->company_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Expense $expense): bool
    {
        return $user->company_id === $expense->company_id;
    }

   
}
