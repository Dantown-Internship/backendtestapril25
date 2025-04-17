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
        return $user->isAdmin() || $user->isManager();
    }

    public function destroy(User $user, Expense $expense): bool
    {
        return $user->isAdmin();
    }

}
