<?php

namespace App\Http\Controllers\V1\Settings\ExpenseCategory;

use App\Actions\ExpenseCategory\CreateExpenseCategoryAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Settings\ExpenseCategory\CreateExpenseCategoryRequest;

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

        unset($createExpenseCategoryRecordOptions['password_confirmation']);

        $createdExpenseCategory = $this->createExpenseCategoryAction->execute(
            $createExpenseCategoryRecordOptions
        );

        return generateSuccessApiMessage('Expense Category was created successfully', 201);
    }
}
