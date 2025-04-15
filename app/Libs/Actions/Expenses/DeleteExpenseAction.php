<?php

namespace App\Libs\Actions\Expenses;

use App\Models\Expense;
use Illuminate\Http\JsonResponse;

class DeleteExpenseAction
{
    public function handle($request, $id): JsonResponse
    {
        $expense = Expense::findOrFail($id);

        $expense->delete();

        return response()->json([
            'message' => 'Expense deleted successfully',
            'success' => true
        ], 200);
    }
}