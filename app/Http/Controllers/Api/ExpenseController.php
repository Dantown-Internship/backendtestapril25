<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\AuditLog;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use Illuminate\Support\Facades\Cache;


class ExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * List expenses (paginated, searchable), cached in Redis.
     */
    public function getExpenseList(Request $request)
    {
        $companyId = $request->user()->company_id;

        $query = Expense::with('user')->where('company_id', $companyId);

        if ($search = $request->query('search')) {
            $query->where(fn($q) =>
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
            );
        }

        $cacheKey = 'expenses_'.$companyId.'_'.md5($request->fullUrl());
        $expenses = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($query) {
            return $query->paginate(10);
        });

        return api_response($expenses, 'Expenses retrieved successfully.');
    }

    /**
     * Create a new expense.
     */
    public function createExpense(StoreExpenseRequest $request)
    {
        $data = $request->validated();

        $expense = Expense::create([
            'company_id' => $request->user()->company_id,
            'user_id'    => $request->user()->id,
            $data,
        ]);

        return api_response($expense, 'Expense created successfully.', true, 201);
    }

    /**
     * Update an expense (Admin & Manager Only).
     */
    public function updateExpense(UpdateExpenseRequest $request, $id)
    {
        $user = $request->user();

        $expense = Expense::where('id', $id)
        ->where('company_id', $user->company_id)
        ->firstOrFail();

        $old = $expense->getOriginal();

        $expense->update($request->validated());

        AuditLog::create([
            'user_id'    => $user->id,
            'company_id' => $user->company_id,
            'action'     => 'update_expense',
            'changes'    => ['old' => $old, 'new' => $expense->toArray()],
        ]);

        return api_response($expense, 'Expense updated successfully.');
    }

    /**
     * Delete an expense (Admins only).
     */
    public function deleteExpense(Request $request, $id)
    {
        $user = $request->user();

        $expense = Expense::where('id', $id)
        ->where('company_id', $user->company_id)
        ->firstOrFail();

        AuditLog::create([
            'user_id'    => $user->id,
            'company_id' => $user->company_id,
            'action'     => 'delete_expense',
            'changes'    => $expense->toArray(),
        ]);

        $expense->delete();

        return api_response(null, 'Expense deleted successfully.');
    }

}
