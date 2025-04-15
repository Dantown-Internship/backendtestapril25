<?php

namespace App\Libs\Actions\Expenses;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Models\Expense;
use App\Http\Resources\ExpenseResource;


class GetExpensesAction
{
    /**
     * Handle the action to get all expenses.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection<ExpenseResource>
     */
    public function handle($request): AnonymousResourceCollection
    {
        $expenses = Expense::with('user')->latest()->paginate(RESULT_COUNT);
        return ExpenseResource::collection($expenses)->additional([
            'message' => 'Expenses retrieved successfully',
            'success' => true,
        ]);
    }
}

