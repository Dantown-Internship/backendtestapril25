<?php

namespace App\Http\Controllers\V1\ExpenseManagement;

use App\Actions\Expense\CreateExpenseAction;
use App\Actions\ExpenseCategory\GetExpenseCategoryByIdAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\ExpenseManagement\CreateExpenseRequest;
use App\Http\Resources\V1\ExpenseManagement\GetExpenseResource;
use App\Jobs\BackgroundProcessing\AuditLog\AuditLogActivityBackgroundProcessingJob;

class CreateExpenseController extends Controller
{
    public function __construct(
        private GetExpenseCategoryByIdAction $getExpenseCategoryByIdAction,
        private CreateExpenseAction $createExpenseAction,
    ) {}

    public function __invoke(CreateExpenseRequest $request)
    {
        $loggedInUser = auth('sanctum')->user();

        $createExpenseRecordOptions =  $request->safe()->merge([
            'company_id' => $loggedInUser->company_id,
            'user_id' => $loggedInUser->id,
        ])->all();

        $createdExpense = $this->createExpenseAction->execute(
            $createExpenseRecordOptions
        );

        $expenseCategory = $this->getExpenseCategoryByIdAction->execute($request->expense_category_id);

        dispatch(
            new AuditLogActivityBackgroundProcessingJob([
                'user_id' => $loggedInUser->id,
                'action' => "{$loggedInUser->name} created an expense",
                'changes' => extractObjectPropertiesToKeyPairValues(
                    [
                        'expense_category' => $expenseCategory->name,
                        'title' => $request->title,
                        'amount' => $request->amount
                    ],
                )
            ])
        );

        $responsePayload = new GetExpenseResource($createdExpense);

        return generateSuccessApiMessage('Expense was created successfully', 201, $responsePayload);
    }
}
