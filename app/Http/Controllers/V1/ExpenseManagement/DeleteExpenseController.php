<?php

namespace App\Http\Controllers\V1\ExpenseManagement;

use App\Actions\Expense\DeleteExpenseAction;
use App\Actions\Expense\GetExpenseByIdAction;
use App\Http\Controllers\Controller;

class DeleteExpenseController extends Controller
{
    public function __construct(
        private GetExpenseByIdAction $getExpenseByIdAction,
        private DeleteExpenseAction $deleteExpenseAction
    ) {}

    public function __invoke(string $expenseId)
    {
        $loggedInUser = auth('sanctum')->user();

        $expense = $this->getExpenseByIdAction->execute($expenseId);

        if (is_null($expense) || $loggedInUser->company_id !== $expense->company_id) {
            return generateErrorApiMessage('Expense record does not exists', 404);
        }

        $this->deleteExpenseAction->execute(
            $expenseId
        );

        return generateSuccessApiMessage('Expense was deleted successfully');
    }
}
