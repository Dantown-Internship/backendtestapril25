<?php

namespace App\Libs\Actions\Expenses;

class UpdateExpenseAction
{
    public function handle($request, $id)
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

        return response()->json([
            'message' => 'Expense updated successfully',
            'data' => $expense,
            'success' => true
        ], 200);
    }
}