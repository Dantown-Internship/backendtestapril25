<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the expenses.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Fetch all expenses for the authenticated user's company
        $query = Expense::where('company_id', Auth::user()->company_id);

        // Apply search filters if provided (search by title or category)
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                      ->orWhere('category', 'like', '%' . $search . '%');
            });
        }

        // Paginate the results (10 expenses per page, you can adjust this number)
        $expenses = $query->paginate(10);

        return response()->json($expenses);
    }

    /**
     * Store a newly created expense in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'category' => 'required|string|max:255',
        ]);

        // Create a new expense for the authenticated user's company
        $expense = Expense::create([
            'company_id' => Auth::user()->company_id,
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'amount' => $validated['amount'],
            'category' => $validated['category'],
        ]);

        return response()->json($expense, 201);
    }

    /**
     * Update the specified expense in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Expense $expense)
    {
        // Ensure the authenticated user is authorized to update the expense
        if ($expense->company_id != Auth::user()->company_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Ensure the authenticated user is Admin or Manager for updating
        if (!in_array(Auth::user()->role, ['Admin', 'Manager'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Validate the request
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'category' => 'required|string|max:255',
        ]);

        // Update the expense
        $expense->update($validated);

        return response()->json($expense);
    }

    /**
     * Remove the specified expense from storage.
     *
     * @param  \App\Models\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function destroy(Expense $expense)
    {
        // Ensure the authenticated user is authorized to delete the expense
        if ($expense->company_id != Auth::user()->company_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Ensure the authenticated user is Admin for deletion
        if (Auth::user()->role != 'Admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Delete the expense
        $expense->delete();

        return response()->json(['message' => 'Expense deleted successfully']);
    }
}
// This controller handles the CRUD operations for expenses.
// It includes methods for listing, creating, updating, and deleting expenses.
// The index method allows searching and pagination of expenses.
// The store method creates a new expense.
// The update method allows updating an existing expense.
// The destroy method deletes an expense.
// The controller also includes authorization checks to ensure that only users with the appropriate roles can perform certain actions.
// The index method allows searching and pagination of expenses.
// The store method creates a new expense.
// The update method allows updating an existing expense.
// The destroy method deletes an expense.
// The controller also includes authorization checks to ensure that only users with the appropriate roles can perform certain actions.
// The index method allows searching and pagination of expenses.
// The store method creates a new expense.
// The update method allows updating an existing expense.
// The destroy method deletes an expense.
// The controller also includes authorization checks to ensure that only users with the appropriate roles can perform certain actions.
// The index method allows searching and pagination of expenses.
// The store method creates a new expense.
// The update method allows updating an existing expense.
// The destroy method deletes an expense.
// The controller also includes authorization checks to ensure that only users with the appropriate roles can perform certain actions.
// The index method allows searching and pagination of expenses.
// The store method creates a new expense.
// The update method allows updating an existing expense.
// The destroy method deletes an expense.
// The controller also includes authorization checks to ensure that only users with the appropriate roles can perform certain actions.
// The index method allows searching and pagination of expenses.
// The store method creates a new expense.  
