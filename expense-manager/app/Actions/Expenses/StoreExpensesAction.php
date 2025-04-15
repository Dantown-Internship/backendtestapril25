<?php

namespace App\Actions\Expenses;

use App\Models\Expense;
use Illuminate\Support\Facades\Auth;

class StoreExpensesAction
{
    public function handle(array $data)
    {
        $user = Auth::user();
        $data['user_id'] = $user->id;
        $data['company_id'] = $user->company_id;

        $expense = Expense::Create($data);

        return $expense;

    }
}
