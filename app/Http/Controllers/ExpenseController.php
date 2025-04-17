<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\ExpenseResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Database\Eloquent\Builder;

class ExpenseController extends Controller
{
    use ApiResponse, AuthorizesRequests;

    /**
     * Display a listing of the expenses, cached by company_id and search criteria.
     */
    public function index(Request $request)
    {
        if (Gate::denies('viewAny', Expense::class)) {
            return $this->failure('You do not have permission to view expenses.', 403);
        }

        $companyId = $request->user()->company_id;
        $page = $request->get('page', 1);
        $searchTerm = $request->get('search');

        // Build the cache key based on search parameters
        $cacheKey = "expenses.company.{$companyId}.page.{$page}";
        if ($searchTerm) {
            $cacheKey .= ".search.{$searchTerm}";
        }

        // One hour (60 minutes) cache
        $expenses = Cache::remember($cacheKey, 60 * 60, function () use ($companyId, $searchTerm) {
            return Expense::with('user')
                ->where('company_id', $companyId)
                ->when($searchTerm, function (Builder $query, $searchTerm) {
                    $query->where(function (Builder $query) use ($searchTerm) {
                        $query->where('title', 'LIKE', "%{$searchTerm}%")
                            ->orWhere('category', 'LIKE', "%{$searchTerm}%");
                    });
                })
                ->paginate(24);
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

        $expense = Auth::user()->expenses()->create($validated);

        // Clear cache for this company, because new data has been added.
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
        return $this->success(new ExpenseResource($expense->load('user')), 'Expense fetched successfully.');
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

        // auditing of changes
        $old = $expense->getOriginal();
        $expense->update($validated);
        $new = $expense->getAttributes();
        $expense->auditLog('update', $old, $new);

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

        $old = $expense->getOriginal();
        $expense->delete();
        $expense->auditLog('delete', $old);

        // Clear cache to ensure fresh data next fetch
        $this->clearExpenseCompanyCache($expense->company_id);

        return $this->success(null, 'Expense deleted successfully.');
    }

    /**
     * Clear cache for a specific company's expenses.
     */
    protected function clearExpenseCompanyCache($companyId)
    {
        //first we remove the older values without searches
        $expensesCount = Expense::count();
        $perPage = 24;
        $maxPages = max(1, ceil($expensesCount / $perPage));

        for ($page = 1; $page <= $maxPages; $page++) {
            Cache::forget("expenses.company.{$companyId}.page.{$page}");
        }
        //after we remove the keys including searches.
        Cache::flush();
    }
}