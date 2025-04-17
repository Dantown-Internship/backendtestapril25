<?php

namespace App\Http\Controllers\V1\ExpenseManagement;

use App\Actions\Expense\DeleteExpenseAction;
use App\Actions\Expense\GetExpenseByIdAction;
use App\Http\Controllers\Controller;
use App\Jobs\BackgroundProcessing\AuditLog\AuditLogActivityBackgroundProcessingJob;

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

        dispatch(
            new AuditLogActivityBackgroundProcessingJob([
                'user_id' => $loggedInUser->id,
                'action' => "{$loggedInUser->name} deleted an expense",
                'changes' => extractObjectPropertiesToKeyPairValues([
                    'expense_category' => $expense->expenseCategory->name,
                    'title' => $expense->title,
                    'amount' => $expense->amount
                ])
            ])
        );

        return generateSuccessApiMessage('Expense was deleted successfully');
    }
}
