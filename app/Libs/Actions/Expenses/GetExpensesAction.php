<?php

namespace App\Libs\Actions\Expenses;

use App\Models\Expense;


class GetExpensesAction
{
    public function handle($request)
    {
        $expenses = Expense::with('user')->paginate();

        return response()->json([
            'message' => 'Expenses retrieved successfully',
            'data' => $expenses,
            'success' => true
        ], 200);
    }
}

