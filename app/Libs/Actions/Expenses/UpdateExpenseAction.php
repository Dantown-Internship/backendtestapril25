<?php

namespace App\Libs\Actions\Expenses;

use App\Models\Expense;
use App\Http\Resources\ExpenseResource;

class UpdateExpenseAction
{
    public function handle($request, $id): ExpenseResource
    {
        $expense = Expense::findOrFail($id);

        $expense->update([
            'amount' => $request->amount,
            'title' => $request->title,
            'category' => $request->category
        ]);

        return ExpenseResource::make($expense)->additional([
            'message' => 'Expense updated successfully',
            'success' => true,
        ]);
    }
}