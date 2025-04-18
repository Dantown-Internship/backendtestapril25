<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $company_id = $user->company_id;

        // Get query parameters
        $searchTerm = $request->query('search');
        $category = $request->query('category');
        $perPage = $request->query('per_page', 15);

        // Create a cache key based on the request parameters
        $cacheKey = "expenses_company_{$company_id}_search_{$searchTerm}_category_{$category}_page_{$request->page}_perPage_{$perPage}";

        // Try to get from cache first
        return Cache::remember($cacheKey, 60, function () use ($user, $company_id, $searchTerm, $category, $perPage) {
            $query = Expense::where('company_id', $company_id)
                ->with('user:id,name,email'); // Eager load user info

            if ($searchTerm) {
                $query->where('title', 'like', "%{$searchTerm}%");
            }

            if ($category) {
                $query->where('category', $category);
            }

            $expenses = $query->latest()->paginate($perPage);

            return response()->json([
                'message' => 'Expenses retrieved successfully',
                'data' => $expenses,
            ]);
        });
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
        ]);

        $user = $request->user();

        $expense = Expense::create([
            'company_id' => $user->company_id,
            'user_id' => $user->id,
            'title' => $validated['title'],
            'amount' => $validated['amount'],
            'category' => $validated['category'],
        ]);

        // Clear cache for this company's expenses
        $this->clearExpenseCache($user->company_id);

        return response()->json([
            'message' => 'Expense created successfully',
            'data' => $expense->load('user:id,name,email'),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'amount' => 'sometimes|required|numeric|min:0',
            'category' => 'sometimes|required|string|max:255',
        ]);

        $user = $request->user();

        // Find the expense within the user's company
        $expense = Expense::where('id', $id)
            ->where('company_id', $user->company_id)
            ->first();

        if (!$expense) {
            return response()->json(['message' => 'Expense not found'], 404);
        }

        // Check if user has permission to update
        if ($user->isEmployee()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Keep old values for audit log
        $oldValues = $expense->toArray();

        // Update expense
        $expense->update([
            'title' => $validated['title'],
            'amount' => $validated['amount'],
            'category' => $validated['category'],
        ]);

        // Create audit log
        AuditLog::create([
            'user_id' => $user->id,
            'company_id' => $user->company_id,
            'action' => 'expense_updated',
            'changes' => [
                'old' => $oldValues,
                'new' => $expense->toArray(),
            ],
            'created_at' => now(),
        ]);

        // Clear cache for this company's expenses
        $this->clearExpenseCache($user->company_id);

        return response()->json([
            'message' => 'Expense updated successfully',
            'data' => $expense->fresh()->load('user:id,name,email'),
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        // Check if user is an admin
        if (!$user->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Find the expense within the user's company
        $expense = Expense::where('id', $id)
            ->where('company_id', $user->company_id)
            ->first();

        if (!$expense) {
            return response()->json(['message' => 'Expense not found'], 404);
        }

        // Keep old values for audit log
        $oldValues = $expense->toArray();

        // Delete expense
        $expense->delete();

        // Create audit log
        AuditLog::create([
            'user_id' => $user->id,
            'company_id' => $user->company_id,
            'action' => 'expense_deleted',
            'changes' => [
                'old' => $oldValues,
                'new' => null,
            ],
            'created_at' => now(),
        ]);

        // Clear cache for this company's expenses
        $this->clearExpenseCache($user->company_id);

        return response()->json(['message' => 'Expense deleted successfully']);
    }

    /**
     * Clear cache for a company's expenses.
     *
     * @param  int  $company_id
     * @return void
     */
    private function clearExpenseCache($company_id)
    {
        // Get all cache keys for this company's expenses
        $pattern = "expenses_company_{$company_id}_*";
        $keys = Cache::get($pattern);

        // If using Redis, you can use the following to delete pattern-matched keys
        // Cache::getRedis()->command('KEYS', [$pattern])->each(function ($key) {
        //     Cache::forget($key);
        // });

        // For simplicity, we'll just forget a few common keys
        Cache::forget("expenses_company_{$company_id}_search__category__page_1_perPage_15");
        Cache::forget("expenses_company_{$company_id}_search__category__page_1_perPage_10");
        Cache::forget("expenses_company_{$company_id}_search__category__page_1_perPage_25");
    }
}
