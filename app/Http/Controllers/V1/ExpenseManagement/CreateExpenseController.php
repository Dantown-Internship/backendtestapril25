<?php

namespace App\Http\Controllers\V1\ExpenseManagement;

use App\Actions\Expense\CreateExpenseAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\ExpenseManagement\CreateExpenseRequest;
use App\Http\Resources\V1\ExpenseManagement\GetExpenseResource;

class CreateExpenseController extends Controller
{
    public function __construct(
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

        $responsePayload = new GetExpenseResource($createdExpense);

        return generateSuccessApiMessage('Expense was created successfully', 201, $responsePayload);
    }
}
