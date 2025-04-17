<?php

namespace App\Http\Controllers\V1\ExpenseManagement;

use App\Actions\Expense\GetExpenseByIdAction;
use App\Actions\Expense\UpdateExpenseAction;
use App\Actions\ExpenseCategory\GetExpenseCategoryByIdAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\ExpenseManagement\UpdateExpenseRequest;
use App\Jobs\BackgroundProcessing\AuditLog\AuditLogActivityBackgroundProcessingJob;

class UpdateExpenseController extends Controller
{
    public function __construct(
        private GetExpenseCategoryByIdAction $getExpenseCategoryByIdAction,
        private GetExpenseByIdAction $getExpenseByIdAction,
        private UpdateExpenseAction $updateExpenseAction
    ) {}

    public function __invoke(UpdateExpenseRequest $request, string $expenseId)
    {
        $loggedInUser = auth('sanctum')->user();

        $expense = $this->getExpenseByIdAction->execute($expenseId);

        if (is_null($expense) || $loggedInUser->company_id !== $expense->company_id) {
            return generateErrorApiMessage('Expense record does not exists', 404);
        }

        $updateUserPayload = $request->validated();

        $this->updateExpenseAction->execute([
            'id' => $expenseId,
            'data' => $updateUserPayload
        ]);

        $expenseCategory = $this->getExpenseCategoryByIdAction->execute($request->expense_category_id);

        dispatch(
            new AuditLogActivityBackgroundProcessingJob([
                'user_id' => $loggedInUser->id,
                'action' => "{$loggedInUser->name} updated an expense",
                'changes' => extractObjectPropertiesToKeyPairValues([
                    'previous_value' => [
                        'expense_category' => $expense->expenseCategory->name,
                        'title' => $expense->title,
                        'amount' => $expense->amount
                    ],
                    'current_value' => [
                        'expense_category' => $expenseCategory->name,
                        'title' => $request->title,
                        'amount' => $request->amount
                    ],
                ])
            ])
        );

        return generateSuccessApiMessage('Expense was updated successfully');
    }
}
