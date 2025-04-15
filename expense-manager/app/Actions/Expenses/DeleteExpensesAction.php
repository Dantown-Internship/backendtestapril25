<?php

namespace App\Actions\Expenses;

use App\Models\Expense;

class DeleteExpensesAction
{
    public function handle($id)
    {
        $expense = Expense::findOrFail($id);
        $expense->delete();
    }
}
