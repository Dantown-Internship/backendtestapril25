<?php

namespace App\Actions\Expenses;

use App\Models\Expense;
use Illuminate\Support\Facades\DB;

class GetExpensesAction
{
    public function handle($request)
    {
        $user = $request->user();
        $company = $user->company;

        // get expenses
        $expenses = Expense::whereCompanyId($company->id)->paginate(20);
        return $expenses;
    }
}
