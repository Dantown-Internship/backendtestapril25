<?php

namespace App\Http\Controllers\V1\ExpenseManagement;

use App\Actions\Expense\GetExpenseByIdAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ExpenseManagement\GetExpenseResource;

class GetExpenseController extends Controller
{
    public function __construct(
        private GetExpenseByIdAction $getExpenseByIdAction
    ) {}

    public function __invoke(string $expenseId)
    {
        $loggedInUser = auth('sanctum')->user();

        $expense = $this->getExpenseByIdAction->execute($expenseId);

        if (is_null($expense) || $loggedInUser->company_id !== $expense->company_id) {
            return generateErrorApiMessage('Expense record does not exists', 404);
        }

        $mutatedExpense = new GetExpenseResource($expense);

        return generateSuccessApiMessage('Expense was retrieved successfully', 200, $mutatedExpense);
    }
}
