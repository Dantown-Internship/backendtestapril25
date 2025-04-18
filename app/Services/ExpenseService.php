<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\User;

class ExpenseService
{
    public function getUserExpenses(User $user, array $filters = [])
    {
        return $user->expenses()->filter($filters);
    }

    public function getCompanyExpenses(User $user, array $filters = [])
    {
        return $user->company->expenses()->filter($filters)
            ->with(['user:id,name', 'company:id,name'])
            ->orderBy('created_at', 'desc');
    }

    public function createExpense(User $user, array $data)
    {
        $data['user_id'] = $user->id;
        return $user->company->expenses()->create($data);
    }

    public function updateExpense(Expense $expense, array $data)
    {
        $expense->update($data);
        return $expense->fresh();
    }

    public function deleteExpense(Expense $expense)
    {
        return $expense->delete();
    }
}
