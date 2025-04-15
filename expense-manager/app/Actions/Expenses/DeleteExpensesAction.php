<?php

namespace App\Actions\Expenses;

use App\Models\Expense;
use Illuminate\Support\Facades\DB;

class DeleteExpensesAction
{
    public function handle($id)
    {
        $expense = Expense::findOrFail($id);
        $expense->delete();
    }
}
