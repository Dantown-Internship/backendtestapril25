<?php

namespace App\Libs\Actions\Expenses;

use App\Models\Expense;
use App\Http\Resources\ExpenseResource;

class UpdateExpenseAction
{
    public function handle($request, $id): ExpenseResource
    {
        $user = $request->user();
        $company = $request->currentCompany;

        $expense = $company->expenses()->findOrFail($id);

        if ($expense->user_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized',
                'success' => false
            ], 403);
        }

        $expense->update([
            'amount' => $request->amount,
            'description' => $request->description,
        ]);

        return ExpenseResource::make($expense)->additional([
            'message' => 'Expense updated successfully',
            'success' => true,
        ]);
    }
}