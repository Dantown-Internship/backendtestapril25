<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    // Get all expenses (with search and pagination)
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Expense::where('company_id', $user->company_id);

        // Search by title or category
        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                  ->orWhere('category', 'like', '%' . $searchTerm . '%');
            });
        }

        $expenses = $query->with('user')->paginate(10); // Eager load to optimize queries
        return response()->json($expenses);
    }

    // Create a new expense
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'category' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        Expense::create([
            'title' => $request->title,
            'amount' => $request->amount,
            'category' => $request->category,
            'company_id' => Auth::user()->company_id,
            'user_id' => Auth::id(), 
        ]);

        return response()->json(['message' => 'Expense created successfully'], 201);
    }

    // Update an existing expense
    public function update(Request $request, Expense $expense)
    {
        // Ensure expense belongs to user's company
        if ($expense->company_id !== Auth::user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255',
            'amount' => 'numeric',
            'category' => 'string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $expense->update($request->all());
        return response()->json(['message' => 'Expense updated successfully'], 200);
    }

    // Delete an expense
    public function destroy(Expense $expense)
    {
        // Authorization check
        if ($expense->company_id !== Auth::user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $expense->delete();
        return response()->json(['message' => 'Expense deleted successfully'], 200);
    }

    // Get expenses for the logged-in employee
    public function employeeExpenses()
    {
        $expenses = Expense::where('user_id', Auth::id())->with('user')->get();
        return response()->json($expenses);
    }

    // Employee-specific expense creation
    public function employeeStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'category' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        Expense::create([
            'title' => $request->title,
            'amount' => $request->amount,
            'category' => $request->category,
            'company_id' => Auth::user()->company_id,
            'user_id' => Auth::id(),
        ]);

        return response()->json(['message' => 'Expense created successfully'], 201);
    }
}