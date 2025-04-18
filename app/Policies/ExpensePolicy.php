<?php

namespace App\Policies;

use App\Models\Expense;
use App\Models\User;

class ExpensePolicy
{
    public function view(User $user, Expense $expense): bool
    {
        return $user->company_id === $expense->company_id;
    }

    public function create(User $user): bool
    {
        return true; // All authenticated users can create expenses
    }

    public function update(User $user, Expense $expense): bool
    {
        return $user->company_id === $expense->company_id && 
               ($user->isAdmin() || $user->isManager());
    }

    public function delete(User $user, Expense $expense): bool
    {
        return $user->company_id === $expense->company_id && $user->isAdmin();
    }
}