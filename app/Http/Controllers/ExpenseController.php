<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
{
    $user = $request->user(); // preferred in controller methods

    $search = $request->input('search');

    $expenses = Expense::where('company_id', $user->company_id)
        ->when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    return response()->json($expenses);
}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'category' => 'nullable|string|max:255',
        ]);

        $validated['company_id'] = $request->user()->company_id;

        $expense = Expense::create($validated);

        return response()->json(['message' => 'Expense recorded successfully', 'expense' => $expense]);
    }

    public function show(Request $request, $id)
    {
        $expense = Expense::find($id);

        if (!$expense || $expense->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Expense not found or unauthorized'], 404);
        }

        return response()->json($expense);
    }

    public function update(Request $request, $id)
    {
        $expense = Expense::find($id);

        if (!$expense || $expense->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Expense not found or unauthorized'], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'amount' => 'sometimes|required|numeric',
            'date' => 'sometimes|required|date',
            'category' => 'nullable|string|max:255',
        ]);

        $expense->update($validated);

        return response()->json(['message' => 'Expense updated', 'expense' => $expense]);
    }

    public function destroy(Request $request, $id)
    {
        $expense = Expense::find($id);

        if (!$expense || $expense->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Expense not found or unauthorized'], 404);
        }

        $expense->delete();

        return response()->json(['message' => 'Expense deleted']);
    }
}
