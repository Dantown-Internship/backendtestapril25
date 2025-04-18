<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Services\Services\ExpenseService;
use Illuminate\Http\JsonResponse;

class ExpenseController extends Controller
{
    protected $expenseService;

    public function __construct(ExpenseService $expenseService)
    {
        $this->expenseService = $expenseService;
    }

    public function index(): JsonResponse
    {
        $expenses = $this->expenseService->getForCompany(auth()->user()->company_id);

        return response()->json($expenses);
    }

    public function store(CreateExpenseRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $expense = $this->expenseService->createExpense($validated);

        return $expense;
    }

    public function update(UpdateExpenseRequest $request, $id): JsonResponse
    {
        $validated = $request->validated();

        $expense = $this->expenseService->updateExpense($id, $validated);

        return $expense;
    }

    public function destroy($id): JsonResponse
    {
        $expense = $this->expenseService->delete($id);

        return $expense;
    }
}
