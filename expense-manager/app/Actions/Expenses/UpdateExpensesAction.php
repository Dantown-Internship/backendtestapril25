<?php

namespace App\Actions\Expenses;

use App\Models\Expense;
use Illuminate\Support\Facades\DB;

class UpdateExpensesAction
{
    public function handle($id, $validated)
    {
        $expense = Expense::findOrFail($id);
        $expense->update($validated);

        return $expense;
    }
}
