<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::where('company_id', $request->user()->company_id)
            ->with('user')
            ->when($request->search, function ($q) use ($request) {
                return $q->where('title', 'like', "%{$request->search}%")
                         ->orWhere('category', 'like', "%{$request->search}%");
            });

        $cacheKey = 'expenses_' . $request->user()->company_id . '_' . md5($request->fullUrl());
        $expenses = Cache::remember($cacheKey, 60, function () use ($query) {
            return $query->paginate(10);
        });

        return response()->json($expenses);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'amount' => 'required|numeric',
            'category' => 'required|string',
        ]);

        $expense = Expense::create([
            'company_id' => $request->user()->company_id,
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'amount' => $validated['amount'],
            'category' => $validated['category'],
        ]);

        return response()->json($expense, 201);
    }

    public function update(Request $request, Expense $expense)
    {
        $this->authorizeCompany($expense, $request->user());

        $validated = $request->validate([
            'title' => 'string',
            'amount' => 'numeric',
            'category' => 'string',
        ]);

        $expense->update($validated);

        return response()->json($expense);
    }

    public function destroy(Expense $expense, Request $request)
{
    \Log::info('Delete expense start', ['expense_id' => $expense->id]);

    try {
        $authUser = $request->user();

        $this->authorizeCompany($expense, $authUser);

        $expenseData = $expense->only(['id', 'title', 'amount', 'category', 'date', 'company_id']);

        $expense->delete();

        \App\Models\AuditLog::create([
            'user_id' => $authUser->id,
            'action' => 'delete',
            'model_type' => 'Expense',
            'model_id' => $expense->id,
            'company_id' => $expense->company_id,
            'changes' => json_encode($expenseData),
            'performed_at' => now(),
        ]);

        \Log::info('Delete expense success', ['expense_id' => $expense->id]);

        return response()->json(['message' => 'Expense deleted successfully']);
    } catch (\Exception $e) {
        \Log::error('Delete expense error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


    // protected function authorizeCompany(Expense $expense, $user)
    // {
    //     if ($expense->company_id !== $user->company_id) {
            
    //         abort(403, 'Unauthorized');
    //     }
    // }

    protected function authorizeCompany(Expense $expense, $user)
{
    \Log::info('Company authorization check for expense delete', [
        'auth_user_company' => $user->company_id,
        'expense_company' => $expense->company_id,
        'user_id' => $user->id,
        'expense_id' => $expense->id,
    ]);

    if ($expense->company_id !== $user->company_id) {
        abort(403, 'Unauthorized');
    }
}

}
