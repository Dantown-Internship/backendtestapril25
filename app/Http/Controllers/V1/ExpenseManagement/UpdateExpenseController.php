<?php

namespace App\Http\Controllers\V1\ExpenseManagement;

use App\Actions\Expense\GetExpenseByIdAction;
use App\Actions\Expense\UpdateExpenseAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\ExpenseManagement\UpdateExpenseRequest;

class UpdateExpenseController extends Controller
{
    public function __construct(
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

        return generateSuccessApiMessage('Expense was updated successfully');
    }
}
