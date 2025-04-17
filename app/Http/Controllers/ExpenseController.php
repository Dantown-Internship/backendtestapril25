<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Models\Expense;
use App\Services\ExpenseService;
use App\Traits\CacheHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ExpenseController extends Controller
{
    use CacheHandler;

    public function index(Request $request, ExpenseService $expenseService)
    {
        $user = $request->user();

        $filters = $request->all();

        $baseKey = 'expenses:company:' . $user->company_id;
        $cacheKey = $this->makeCacheKey($baseKey, $filters);

        $expenses = $this->cache($cacheKey, function () use ($expenseService, $user, $filters) {
            return $expenseService->getCompanyExpenses($user, $filters)->paginate(10);
        });

        return successJsonResponse('Expenses retrieved successfully.', $expenses);
    }

    public function store(CreateExpenseRequest $request, ExpenseService $expenseService)
    {
        $data = $request->validated();

        $user = $request->user();

        $expense = $expenseService->createExpense($user, $data);

        return successJsonResponse('Expense added successfully.', ['expense' => $expense]);
    }

    public function update(UpdateExpenseRequest $request, Expense $expense, ExpenseService $expenseService)
    {
        Gate::authorize('update', $expense);

        $data = $request->validated();

        $user = $request->user();

        $expense = $expenseService->updateExpense($user, $data);

        return successJsonResponse('Expense updated successfully.');
    }

    public function destroy(Request $request, Expense $expense, ExpenseService $expenseService)
    {
        Gate::authorize('delete', $expense);

        $expenseService->deleteExpense($expense);

        return successJsonResponse('Expenses deleted successfully.');
    }
}
