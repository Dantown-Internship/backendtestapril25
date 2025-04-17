<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Expense;

class ExpensePolicy
{

    public function __construct()
    {
        //
    }

    public function update(User $user, Expense $expense)
    {
        return $user->role === 'Admin' || ($user->role === 'Manager' && $user->company_id === $expense->company_id);
    }

    public function delete(User $user, Expense $expense)
    {
        return $user->role === 'Admin' && $user->company_id === $expense->company_id;
    }

}
