<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Jobs\LogExpenseJob;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $search = $request->input('search');
        $cacheKey = 'expenses_user_' . $user->id;

        if (!$search) {
            $expenses = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($user) {
                return Expense::with('user')
                    ->where('company_id', $user->company_id)
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);
            });
        } else {
            $expenses = Expense::with('user')
                ->where('company_id', $user->company_id)
                ->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('category', 'like', "%{$search}%");
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }

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
        $validated['user_id'] = $request->user()->id;

        $expense = Expense::create($validated);

        Cache::forget('expenses_user_' . $request->user()->id);

        LogExpenseJob::dispatch($expense);

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

        $oldValues = $expense->toArray();

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'amount' => 'sometimes|required|numeric',
            'date' => 'sometimes|required|date',
            'category' => 'nullable|string|max:255',
        ]);

        $expense->update($validated);

        Cache::forget('expenses_user_' . $request->user()->id);

        AuditLog::create([
            'user_id' => $request->user()->id,
            'company_id' => $request->user()->company_id,
            'action' => 'updated',
            'changes' => [
                'old' => $oldValues,
                'new' => $expense->fresh()->toArray(),
            ],
        ]);

        return response()->json(['message' => 'Expense updated', 'expense' => $expense]);
    }

    public function destroy(Request $request, $id)
    {
        $expense = Expense::find($id);

        if (!$expense || $expense->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Expense not found or unauthorized'], 404);
        }

        $oldValues = $expense->toArray();

        $expense->delete();

        Cache::forget('expenses_user_' . $request->user()->id);

        AuditLog::create([
            'user_id' => $request->user()->id,
            'company_id' => $request->user()->company_id,
            'action' => 'deleted',
            'changes' => [
                'old' => $oldValues,
                'new' => null,
            ],
        ]);

        return response()->json(['message' => 'Expense deleted']);
    }
}
