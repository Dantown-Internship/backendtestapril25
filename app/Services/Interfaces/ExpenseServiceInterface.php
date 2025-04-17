<?php

namespace App\Services\Interfaces;

use App\Models\Expense;

interface ExpenseServiceInterface
{
    public function createExpense(array $data): Expense;
    public function updateExpense(Expense $expense, array $data): bool;
    public function deleteExpense(Expense $expense): bool;
}