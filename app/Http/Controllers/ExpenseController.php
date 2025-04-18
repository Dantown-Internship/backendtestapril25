<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Models\Expense;
use App\Services\ExpenseService;
use App\Traits\Auditable;
use App\Traits\CacheHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ExpenseController extends Controller
{
    use CacheHandler, Auditable;

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

        return successJsonResponse('Expense added successfully.', ['expense' => $expense], 201);
    }

    public function update(UpdateExpenseRequest $request, Expense $expense, ExpenseService $expenseService)
    {
        Gate::authorize('update', $expense);

        $data = $request->validated();

        $user = $request->user();

        if (isset($data['amount'])) {
            $data['amount'] = number_format($data['amount'], 2, '.', '');
        }

        $old = clone $expense;
        $expense->fill($data);

        // audit log if any of the relevant fields were changed
        if ($expense->isDirty()) {
            $new = $expenseService->updateExpense($expense, $data);
            $this->logAudit($user, 'update', $old, $new);
        }
        return successJsonResponse('Expense updated successfully.', ['expense' => $expense]);
    }

    public function destroy(Request $request, Expense $expense, ExpenseService $expenseService)
    {
        Gate::authorize('delete', $expense);

        $expenseService->deleteExpense($expense);

        $this->logAudit($request->user(), 'delete', $expense);

        return successJsonResponse('Expenses deleted successfully.');
    }
}
