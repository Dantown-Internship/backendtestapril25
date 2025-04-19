<?php

namespace App\Services;

use App\Models\Expense;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ExpenseService
{

    public function __construct()
    {
        // Code here
    }

    /**
     * Get all expenses for the authenticated user.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllExpenses($request): LengthAwarePaginator
    {
        $perPage = $request->input('per_page', 10);

        return Expense::query()
            ->where('company_id', auth()->user()->company_id)
            ->when($request->title, fn($q) => $q->where('title', 'like', "%{$request->title}%"))
            ->when($request->category, fn($q) => $q->where('category', 'like', "%{$request->category}%"))
            ->with('user:id,name')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Create a new expense.
     *
     * @param \Illuminate\Http\Request $request
     * @return \App\Models\Expense
     */
    public function createExpense($request)
    {
        // Validate the request
        $validated = $request->validated();

        // Create a new expense
        $expense = Expense::create([
            'title' => $validated['title'],
            'amount' => $validated['amount'],
            'category' => $validated['category'],
            'user_id' => auth()->id(),
            'company_id' => auth()->user()->company_id,
        ]);

        // Return the created expense
        return $expense;
    }

    /**
     * Update an existing expense.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Expense $expense
     * @return \App\Models\Expense
     */
    public function updateExpense($request, Expense $expense)
    {
        // Validate the request
        $validated = $request->validated();

        // Update the expense
        $expense->update([
            'title' => $validated['title'],
            'amount' => $validated['amount'],
            'category' => $validated['category'],
        ]);

        // Return the updated expense
        return $expense;
    }

    /**
     * Delete an existing expense.
     *
     * @param \App\Models\Expense $expense
     * @return bool
     */
    public function deleteExpense(Expense $expense)
    {
        // Delete the expense
        return $expense->delete();
    }
}
