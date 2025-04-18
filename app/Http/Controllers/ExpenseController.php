<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::query()
            ->where('company_id', $request->company_id)
            ->with('user:id,name,email'); // Eager loading to avoid N+1

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        // Cache the results for performance
        $cacheKey = "expenses:{$request->company_id}:{$request->search}:{$request->category}:{$request->page}";
        $expenses = Cache::remember($cacheKey, 300, function() use ($query) {
            return $query->paginate(15);
        });

        return response()->json($expenses);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string|max:100',
        ]);

        $expense = Expense::create([
            'company_id' => $request->company_id,
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'amount' => $validated['amount'],
            'category' => $validated['category'],
        ]);

        // Clear cache after creation
        $this->clearExpenseCache($request->company_id);

        return response()->json($expense, 201);
    }

    public function show(Request $request, Expense $expense)
    {
        // Check if expense belongs to user's company
        if ($expense->company_id !== $request->company_id) {
            return response()->json(['message' => 'Expense not found'], 404);
        }

        return response()->json($expense->load('user:id,name,email'));
    }

    public function update(Request $request, Expense $expense)
    {
        // Check if expense belongs to user's company
        if ($expense->company_id !== $request->company_id) {
            return response()->json(['message' => 'Expense not found'], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'amount' => 'sometimes|required|numeric|min:0',
            'category' => 'sometimes|required|string|max:100',
        ]);

        // Save the old state for audit log
        $oldExpense = $expense->toArray();

        // Update the expense
        $expense->update($validated);

        // Create audit log entry
        AuditLog::create([
            'user_id' => $request->user()->id,
            'company_id' => $request->company_id,
            'action' => 'expense.update',
            'changes' => [
                'old' => $oldExpense,
                'new' => $expense->toArray(),
            ],
        ]);

        // Clear cache after update
        $this->clearExpenseCache($request->company_id);

        return response()->json($expense);
    }

    public function destroy(Request $request, Expense $expense)
    {
        // Check if expense belongs to user's company
        if ($expense->company_id !== $request->company_id) {
            return response()->json(['message' => 'Expense not found'], 404);
        }

        // Save the old state for audit log
        $oldExpense = $expense->toArray();

        // Delete the expense
        $expense->delete();

        // Create audit log entry
        AuditLog::create([
            'user_id' => $request->user()->id,
            'company_id' => $request->company_id,
            'action' => 'expense.delete',
            'changes' => [
                'old' => $oldExpense,
                'new' => null,
            ],
        ]);

        // Clear cache after deletion
        $this->clearExpenseCache($request->company_id);

        return response()->json(['message' => 'Expense deleted successfully']);
    }

    private function clearExpenseCache($companyId)
    {
        // Clear all expense caches for the company
        // In a real application, a more targeted approach might be better
        Cache::forget("expenses:{$companyId}:*");
    }
}