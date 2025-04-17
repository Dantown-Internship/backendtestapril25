<?php

namespace App\Modules\Expense\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Modules\Expense\Dtos\ExpenseDto;
use App\Modules\Expense\Services\ExpenseService;
use App\Traits\ApiResponsesTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ExpenseController extends Controller
{
    use ApiResponsesTrait;

    public function __construct(private readonly ExpenseService $expenseService) {}

    public function store(Request $request)
    {
        $this->authorize('create', Expense::class);

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string|max:100',
        ]);
        $validatedData['user_id'] = $request->user()->id;
        $validatedData['company_id'] = $request->user()->company_id;

        $response = $this->expenseService->create(ExpenseDto::fromArray($validatedData));

        if (!$response) {
            return $this->errorApiResponse(
                'An error has occurred, please try again.',
                Response::HTTP_SERVICE_UNAVAILABLE
            );
        }
        return $this->successApiResponse(
            $response['message'],
            $response['data'],
            Response::HTTP_CREATED
        );
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Expense::class);

        $companyId = $request->user()->company_id;
        $perPage = $request->query('per_page', 15);
        $searchQuery = $request->query('search', '');
        $categoryFilter = $request->query('category', '');

        $response = $this->expenseService->list(
            $companyId,
            $perPage,
            $searchQuery,
            $categoryFilter,
            $request
        );

        if (!$response) {
            return $this->errorApiResponse(
                'An error has occurred, please try again.',
                Response::HTTP_SERVICE_UNAVAILABLE
            );
        }
        return $this->successApiResponse(
            $response['message'],
            $response['data'],
            Response::HTTP_OK
        );
    }

    public function edit(Request $request, Expense $expense)
    {

        $this->authorize('update', $expense);

        $validatedData = $request->validate([
            'title' => 'sometimes|string|max:255',
            'amount' => 'sometimes|numeric|min:0',
            'category' => 'sometimes|string|max:100',
        ]);

        $response = $this->expenseService->update($validatedData, $expense, $request);

        if (!$response) {
            return $this->errorApiResponse(
                'An error has occurred, please try again.',
                Response::HTTP_SERVICE_UNAVAILABLE
            );
        }
        return $this->successApiResponse(
            $response['message'],
            $response['data'],
            Response::HTTP_OK
        );
    }

    public function destroy(Request $request, Expense $expense)
    {
        $this->authorize('delete', $expense);

        $response = $this->expenseService->delete($expense, $request);
        
        return $this->successNoDataApiResponse(
            $response['message'],
            Response::HTTP_NO_CONTENT
        );
    }
}
