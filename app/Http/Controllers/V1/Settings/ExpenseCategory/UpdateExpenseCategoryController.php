<?php

namespace App\Http\Controllers\V1\Settings\ExpenseCategory;

use App\Actions\ExpenseCategory\GetExpenseCategoryByIdAction;
use App\Actions\ExpenseCategory\UpdateExpenseCategoryAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Settings\ExpenseCategory\UpdateExpenseCategoryRequest;
use App\Jobs\BackgroundProcessing\AuditLog\AuditLogActivityBackgroundProcessingJob;

class UpdateExpenseCategoryController extends Controller
{
    public function __construct(
        private GetExpenseCategoryByIdAction $getExpenseCategoryByIdAction,
        private UpdateExpenseCategoryAction $updateExpenseCategoryAction
    ) {}

    public function __invoke(UpdateExpenseCategoryRequest $request, string $expenseCategoryId)
    {
        $loggedInUser = auth('sanctum')->user();

        $expenseCategory = $this->getExpenseCategoryByIdAction->execute($expenseCategoryId);

        if (is_null($expenseCategory) || $loggedInUser->company_id !== $expenseCategory->company_id) {
            return generateErrorApiMessage('Expense category record does not exists', 404);
        }

        $updateUserPayload = $request->validated();

        $this->updateExpenseCategoryAction->execute([
            'id' => $expenseCategoryId,
            'data' => $updateUserPayload
        ]);

        dispatch(
            new AuditLogActivityBackgroundProcessingJob([
                'user_id' => $loggedInUser->id,
                'action' => "{$loggedInUser->name} updated an expense category",
                'changes' => extractObjectPropertiesToKeyPairValues(
                    [
                        'previous_value' => [
                            'name' => $expenseCategory->name
                        ],
                        'current_value' => [
                            'name' => $request->name
                        ],
                    ]
                )
            ])
        );

        return generateSuccessApiMessage('Expense category was updated successfully');
    }
}
