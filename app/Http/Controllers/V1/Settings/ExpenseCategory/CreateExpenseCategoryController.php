<?php

namespace App\Http\Controllers\V1\Settings\ExpenseCategory;

use App\Actions\ExpenseCategory\CreateExpenseCategoryAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Settings\ExpenseCategory\CreateExpenseCategoryRequest;
use App\Jobs\BackgroundProcessing\AuditLog\AuditLogActivityBackgroundProcessingJob;

class CreateExpenseCategoryController extends Controller
{
    public function __construct(
        private CreateExpenseCategoryAction $createExpenseCategoryAction,
    ) {}

    public function __invoke(CreateExpenseCategoryRequest $request)
    {
        $loggedInUser = auth('sanctum')->user();

        $createExpenseCategoryRecordOptions =  $request->safe()->merge([
            'company_id' => $loggedInUser->company_id,
        ])->all();

        $this->createExpenseCategoryAction->execute(
            $createExpenseCategoryRecordOptions
        );

        dispatch(
            new AuditLogActivityBackgroundProcessingJob([
                'user_id' => $loggedInUser->id,
                'action' => "{$loggedInUser->name} created an expense category",
                'changes' => extractObjectPropertiesToKeyPairValues(
                    $request->validated()
                )
            ])
        );


        return generateSuccessApiMessage('Expense Category was created successfully', 201);
    }
}
