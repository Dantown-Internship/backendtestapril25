<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Resources\ExpenseResource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ExpenseController extends Controller
{
    use ApiResponse, AuthorizesRequests;

    /**
     * Display a listing of the expenses, cached by company_id for 1 hour.
     */
    public function index(Request $request)
    {
        if (Gate::denies('viewAny', Expense::class)) {
            return $this->failure('You do not have permission to view expenses.', 403);
        }

        $companyId = $request->user()->company_id;
        $page = $request->get('page', 1);
        $cacheKey = "expenses.company.{$companyId}.page.{$page}";

        // One hour (60 minutes) cache
        $expenses = Cache::remember($cacheKey, 60 * 60, function () use ($companyId) {
            return Expense::where('company_id', $companyId)->paginate(24);
        });

        return $this->success(
            ExpenseResource::collection($expenses)->response()->getData(true),
            'Expenses fetched successfully.'
        );
    }

    /**
     * Store a newly created expense in storage.
     */
    public function store(Request $request)
    {
        if (Gate::denies('create', Expense::class)) {
            return $this->failure('You do not have permission to create an expense.', 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'nullable|string|max:255',
        ]);

        $validated['company_id'] = $request->user()->company_id;

        $expense = Expense::create($validated);

        // Clear cache for this company
        $this->clearExpenseCompanyCache($expense->company_id);

        return $this->success(new ExpenseResource($expense), 'Expense created successfully.', 201);
    }

    /**
     * Display the specified expense.
     */
    public function show(Expense $expense, Request $request)
    {
        if (Gate::denies('view', $expense)) {
            return $this->failure('You do not have permission to view this expense.', 403);
        }
        return $this->success(new ExpenseResource($expense), 'Expense fetched successfully.');
    }

    /**
     * Update the specified expense in storage.
     */
    public function update(Request $request, Expense $expense)
    {
        if (Gate::denies('update', $expense)) {
            return $this->failure('You do not have permission to update this expense.', 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'amount' => 'sometimes|required|numeric|min:0',
            'date' => 'sometimes|required|date',
            'category' => 'nullable|string|max:255',
        ]);

        $expense->update($validated);

        // Clear cache to ensure fresh data next fetch
        $this->clearExpenseCompanyCache($expense->company_id);

        return $this->success(new ExpenseResource($expense), 'Expense updated successfully.');
    }

    /**
     * Remove the specified expense from storage.
     */
    public function destroy(Expense $expense)
    {
        if (Gate::denies('delete', $expense)) {
            return $this->failure('You do not have permission to delete this expense.', 403);
        }

        $expense->delete();

        // Clear cache to ensure fresh data next fetch
        $this->clearExpenseCompanyCache($expense->company_id);

        return $this->success(null, 'Expense deleted successfully.');
    }

    /**
     * Clear cache for a specific company's expenses.
     */
    protected function clearExpenseCompanyCache($companyId)
    {
        $expensesCount = Expense::where('company_id', $companyId)->count();
        $perPage = 24;
        $maxPages = max(1, ceil($expensesCount / $perPage));

        for ($page = 1; $page <= $maxPages; $page++) {
            Cache::forget("expenses.company.{$companyId}.page.{$page}");
        }
    }
}