<?php

namespace App\Http\Controllers\APIS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Expense, AuditLog};

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Expense::where('company_id', $user->company_id);

        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        if ($request->filled('category')) {
            $query->where('category', 'like', '%' . $request->category . '%');
        }

        $expenses = $query->with('user')->paginate(10);

        return response()->json($expenses);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'amount' => 'required|numeric',
            'category' => 'required|string'
        ]);

        $expense = Expense::create([
            'company_id' => $request->user()->company_id,
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'amount' => $request->amount,
            'category' => $request->category,
        ]);

        return response()->json($expense, 201);
    }

    public function update(Request $request, $id)
    {
        $expense = Expense::where('company_id', $request->user()->company_id)->findOrFail($id);
        $old = $expense->toArray();

        $request->validate([
            'title' => 'sometimes|string',
            'amount' => 'sometimes|numeric',
            'category' => 'sometimes|string'
        ]);

        $expense->update($request->only(['title', 'amount', 'category']));

        AuditLog::create([
            'user_id' => $request->user()->id,
            'company_id' => $request->user()->company_id,
            'action' => 'update',
            'changes' => json_encode([
                'before' => $old,
                'after' => $expense->toArray()
            ]),
        ]);

        return response()->json($expense);
    }
    public function destroy(Request $request, $id)
    {
        $expense = Expense::where('company_id', $request->user()->company_id)->findOrFail($id);

        $expenseData = $expense->toArray();

        AuditLog::create([
            'user_id' => $request->user()->id,
            'company_id' => $request->user()->company_id,
            'action' => 'delete',
            'changes' => json_encode(['before' => $expenseData]),
        ]);

        $expense->delete();

        return response()->json(['message' => 'Expense deleted']);
    }
}
