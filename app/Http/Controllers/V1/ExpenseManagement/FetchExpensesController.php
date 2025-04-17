<?php

namespace App\Http\Controllers\V1\ExpenseManagement;

use App\Actions\Expense\ListExpensesAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\ExpenseManagement\FetchExpensesRequest;
use App\Http\Resources\V1\ExpenseManagement\FetchExpensesResource;

class FetchExpensesController extends Controller
{
    public function __construct(
        private ListExpensesAction $listExpensesAction
    ) {}

    public function __invoke(FetchExpensesRequest $request)
    {
        $loggedInUser = auth('sanctum')->user();

        $relationships = ['expenseCategory'];
        ['expense_payload' => $expenses, 'pagination_payload' => $paginationPayload] = $this->listExpensesAction->execute([
            'filter_record_options_payload' => [
                'company_id' => $loggedInUser->company_id,
                'expense_category_id' => $request->expense_category_id ?? null,
                'search_query' => $request->search_query,
            ],
            'pagination_payload' => [
                'page' => $request->page ?? 1,
                'limit' => $request->per_page ?? 20,
            ]
        ], $relationships);

        $mutatedExpenses = FetchExpensesResource::collection($expenses);

        $responsePayload = [
            'expenses' => $mutatedExpenses,
            'pagination_payload' => $paginationPayload
        ];

        return generateSuccessApiMessage('The list of expenses was retrieved successfully', 200, $responsePayload);
    }
}
