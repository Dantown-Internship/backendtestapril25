<?php

namespace App\Http\Controllers\V1\Settings\ExpenseCategory;

use App\Actions\ExpenseCategory\ListExpenseCategoriesAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\Settings\ExpenseCategory\FetchExpenseCategoriesResource;

class FetchExpenseCategoriesController extends Controller
{
    public function __construct(
        private ListExpenseCategoriesAction $listExpenseCategoriesAction
    ) {}

    public function __invoke()
    {
        $loggedInUser = auth('sanctum')->user();

        ['expense_category_payload' => $expenseCategories] = $this->listExpenseCategoriesAction->execute([
            'filter_record_options_payload' => [
                'company_id' => $loggedInUser->company_id,
            ]
        ]);

        $mutatedExpenseCategories = FetchExpenseCategoriesResource::collection($expenseCategories);

        $responsePayload = [
            'expense_categories' => $mutatedExpenseCategories,
        ];

        return generateSuccessApiMessage('The list of expenses was retrieved successfully', 200, $responsePayload);
    }
}
