<?php

namespace App\Actions\Expenses;

use App\Models\Expense;

class UpdateExpensesAction
{
    public function handle($id, $validated)
    {
        $expense = Expense::findOrFail($id);
        $expense->update($validated);

        return $expense;
    }
}
