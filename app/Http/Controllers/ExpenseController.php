<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use App\Models\AuditLog;


class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Expense::where('company_id', $user->company_id);

        // Optional filtering
        if ($request->has('title')) {
            $query->where('title', 'LIKE', '%' . $request->title . '%');
        }

        if ($request->has('category')) {
            $query->where('category', 'LIKE', '%' . $request->category . '%');
        }

        // Eager load user info (optional)
        $expenses = $query->with('user')
                          ->orderBy('created_at', 'desc')
                          ->paginate(10);

        return response()->json($expenses);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string|max:100',
        ]);

        $user = auth()->user();

        $expense = \App\Models\Expense::create([
            'title' => $request->title,
            'amount' => $request->amount,
            'category' => $request->category,
            'user_id' => $user->id,
            'company_id' => $user->company_id,
        ]);

        return response()->json([
            'message' => 'Expense created successfully.',
            'data' => $expense
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        

        // its Only Admin or Manager can update
        if (!in_array($user->role, ['Admin', 'Manager'])) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string|max:100',
        ]);

        //  Finds expense in the same company
        $expense = \App\Models\Expense::where('company_id', $user->company_id)->find($id);

        if (!$expense) {
            return response()->json(['message' => 'Expense not found'], 404);
        }

   
        $expense->update([
            'title' => $request->title,
            'amount' => $request->amount,
            'category' => $request->category,
        ]);

        AuditLog::create([
            'user_id' => $user->id,
            'company_id' => $user->company_id,
            'action' => 'updated',
            'changes' => json_encode([
                'old' => $expense->getOriginal(),
                'new' => $expense->getAttributes()
            ]),
        ]);
        

        return response()->json([
            'message' => 'Expense updated successfully.',
            'data' => $expense
        ]);

        
    }

    public function destroy($id)
    {
        $user = auth()->user();

        // ✅ Only Admin can delete
        if ($user->role !== 'Admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // 🔒 Find the expense from the same company
        $expense = \App\Models\Expense::where('company_id', $user->company_id)->find($id);

        if (!$expense) {
            return response()->json(['message' => 'Expense not found'], 404);
        }

        // 🕵️ Audit log before deleting
        \App\Models\AuditLog::create([
            'user_id' => $user->id,
            'company_id' => $user->company_id,
            'action' => 'deleted',
            'changes' => json_encode([
                'old' => $expense->toArray(),
                'new' => null
            ]),
        ]);

        $expense->delete();

        return response()->json(['message' => 'Expense deleted successfully.']);
    }



}
