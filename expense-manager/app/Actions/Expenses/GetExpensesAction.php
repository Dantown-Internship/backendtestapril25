<?php

namespace App\Actions\Expenses;

use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class GetExpensesAction
{
    public function handle($id)
    {
        $expense = Expense::findOrFail($id);

        Gate::authorize('view', $expense);

        $user = Auth::user();

        return $expense->load('user');
    }
}
