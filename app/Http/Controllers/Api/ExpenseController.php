<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExpenseRequest;
use App\Jobs\WeeklyExpenseReportJob;
use App\Services\ExpenseService;
use App\Traits\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    use ApiResponse, AuthorizesRequests;

    public function __construct(
        private ExpenseService $expenseService
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            WeeklyExpenseReportJob::dispatch();
            $expenses = $this->expenseService->getExpensesForCompany(
                $request->user()->company_id,
                $request->all()
            );
            $message = $expenses->isEmpty() ? 'No expenses  found.' : 'Expenses fetched successfully';

            return $this->successResponse($expenses, $message);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch expenses', 500, $e->getMessage());
        }
    }

    public function store(ExpenseRequest $request): JsonResponse
    {
        try {
            $expense = $this->expenseService->createExpense(
                $request->validated()
            );

            $message = 'Expenses saved successfully';

            return $this->successResponse($expense, $message, 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to save expenses', 500, $e->getMessage());
        }
    }

    public function update(int $id, ExpenseRequest $request): JsonResponse
    {
        try {
            $expense = $this->expenseService->find($id);
            $this->authorize('update', $expense);
            $updatedExpense = $this->expenseService->updateExpense(
                $request->validated(),
                $expense
            );

            $message = 'Expenses updated successfully';

            return $this->successResponse($updatedExpense, $message);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update expenses', 500, $e->getMessage());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $expense = $this->expenseService->find($id);
            $this->authorize('delete',  $expense);
            $this->expenseService->deleteExpense($expense);

            $message = 'Expenses deleted successfully';

            return $this->successResponse(null, $message);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete expenses', 500, $e->getMessage());
        }
    }
}
