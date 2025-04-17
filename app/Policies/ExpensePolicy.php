<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExpensePolicy
{
    use HandlesAuthorization;

    public function update(User $user, Expense $expense): bool
    {
        // Employee can only update their own expenses
        // Managers and Admins can update any expense from their company
        if ($user->role === RoleEnum::EMPLOYEE) {
            return $user->id === $expense->user_id;
        }
        
        return in_array($user->role, [RoleEnum::ADMIN, RoleEnum::MANAGER]) && 
                $this->isMyCompany($user, $expense);
    }

    public function delete(User $user, Expense $expense): bool
    {
        return $user->role === RoleEnum::ADMIN && 
               $this->isMyCompany($user, $expense);
    }

    private function isMyCompany(User $user, Expense $expense) : bool
    {
        return $user->company_id === $expense->user->company_id;
    }
} 