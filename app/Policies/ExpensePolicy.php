<?php

namespace App\Policies;

use App\Models\Expense;
use App\Models\User;

class ExpensePolicy
{

    public function index(User $user): bool
    {
        return true;
    }

    public function store(User $user): bool
    {
        return !empty($user->company);
    }

    public function update(User $user, Expense $expense): bool
    {
        return ($user->isAdmin() || $user->isManager() || $user->id === $expense->user_id) && $user->company_id === $expense->company_id;
    }

    public function destroy(User $user, Expense $expense): bool
    {
        return ($user->isAdmin() || $user->id === $expense->user_id) && $user->company_id === $expense->company_id;
    }

}
