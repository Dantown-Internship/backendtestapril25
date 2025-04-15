<?php

namespace App\Actions\Expenses;

use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GetExpensesAction
{
    public function handle($id)
    {
        $expense = Expense::findOrFail($id);
        $user = Auth::user();
        if($expense->user != $user){
            abort(401, 'Unauthorised');
        }

        // get expenses
        $expense = $expense->whereUserId($user->id)->first();

        return $expense->load('user');
    }
}
