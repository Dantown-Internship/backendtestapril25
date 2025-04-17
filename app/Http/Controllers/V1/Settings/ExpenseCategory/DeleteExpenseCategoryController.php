<?php

namespace App\Http\Controllers\V1\Settings\ExpenseCategory;

use App\Actions\ExpenseCategory\DeleteExpenseCategoryAction;
use App\Actions\ExpenseCategory\GetExpenseCategoryByIdAction;
use App\Http\Controllers\Controller;

class DeleteExpenseCategoryController extends Controller
{
    public function __construct(
        private GetExpenseCategoryByIdAction $getExpenseCategoryByIdAction,
        private DeleteExpenseCategoryAction $deleteExpenseCategoryAction
    ) {}

    public function __invoke(string $expenseCategoryId)
    {
        $loggedInUser = auth('sanctum')->user();

        $expenseCategory = $this->getExpenseCategoryByIdAction->execute($expenseCategoryId);

        if (is_null($expenseCategory) || $loggedInUser->company_id !== $expenseCategory->company_id) {
            return generateErrorApiMessage('Expense category record does not exists', 404);
        }

        $this->deleteExpenseCategoryAction->execute(
            $expenseCategoryId
        );

        return generateSuccessApiMessage('Expense category was deleted successfully');
    }
}
