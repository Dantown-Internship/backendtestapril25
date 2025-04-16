<?php

namespace App\Http\Controllers;

use App\Models\Expenses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\AuditLog;

class ExpensesController extends Controller
{
    public function listExpenses(Request $request)
    {
        $user = Auth::user();
        $query = Expenses::where('company_id', $user->company_id)
            ->with(['user']) // Eager load user to avoid N+1
            ->when($request->search, function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('category', 'like', '%' . $request->search . '%');
            });

        // Cache the results for 10 minutes
        $expenses = Cache::remember(
            'expenses_' . $user->company_id . '_' . md5($request->fullUrl()),
            600,
            fn () => $query->paginate(10)
        );

        return response()->json($expenses);
    }

    public function saveExpenses(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
        ]);

        $user = Auth::user();

        $expense = Expenses::create([
            'company_id' => $user->company_id,
            'user_id' => $user->id,
            'title' => $request->title,
            'amount' => $request->amount,
            'category' => $request->category,
        ]);

        return response()->json($expense, 201);
    }

    public function updateExpenses(Request $request, $id)
    {
        $user = Auth::user();
        $expense = Expenses::where('company_id', $user->company_id)->findOrFail($id);
    
        $request->validate([
            'title' => 'string|max:255',
            'amount' => 'numeric|min:0',
            'category' => 'string|max:255',
        ]);
    
        $oldValues = $expense->only(['title', 'amount', 'category']);
        $expense->update($request->only(['title', 'amount', 'category']));
        $newValues = $expense->only(['title', 'amount', 'category']);
    
        // Log the update
        AuditLog::create([
            'user_id' => $user->id,
            'company_id' => $user->company_id,
            'action' => 'expense_updated',
            'changes' => json_encode(['old' => $oldValues, 'new' => $newValues]),
        ]);
    
        return response()->json($expense);
    }
    
    public function destroyExpenses($id)
    {
        $user = Auth::user();
        $expense = Expenses::where('company_id', $user->company_id)->findOrFail($id);
    
        // Log the deletion
        AuditLog::create([
            'user_id' => $user->id,
            'company_id' => $user->company_id,
            'action' => 'expense_deleted',
            'changes' => json_encode($expense->toArray()),
        ]);
    
        $expense->delete();
    
        return response()->json(['message' => 'Expense deleted']);
    }
}