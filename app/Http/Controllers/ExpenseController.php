<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ExpenseController extends Controller
{
    // GET /api/expenses
    public function index(Request $request)
{
    $cacheKey = 'expenses_user_' . auth()->id() . '_' . md5($request->title . $request->category . $request->page);

    return Cache::remember($cacheKey, 60, function () use ($request) {
        return Expense::with('user')
            ->where('company_id', auth()->user()->company_id)
            ->when($request->title, fn($q) => $q->where('title', 'like', "%{$request->title}%"))
            ->when($request->category, fn($q) => $q->where('category', $request->category))
            ->paginate(10);
    });
}


    // POST /api/expenses
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'amount' => 'required|numeric',
            'category' => 'required|string',
        ]);

        $expense = Expense::create([
            'title' => $validated['title'],
            'amount' => $validated['amount'],
            'category' => $validated['category'],
            'user_id' => Auth::id(),
            'company_id' => Auth::user()->company_id,
        ]);

        return response()->json(['message' => 'Expense created', 'data' => $expense], 201);
    }

    // PUT /api/expenses/{id}
    public function update(Request $request, $id)
    {
        $expense = Expense::where('company_id', Auth::user()->company_id)->findOrFail($id);

        $oldData = $expense->toArray();

        $validated = $request->validate([
            'title' => 'required|string',
            'amount' => 'required|numeric',
            'category' => 'required|string',
        ]);

        $expense->update($validated);

        $newData = $expense->fresh()->toArray();

        AuditLog::create([
            'user_id' => Auth::id(),
            'company_id' => Auth::user()->company_id,
            'action' => 'updated expense',
            'changes' => json_encode([
                'before' => $oldData,
                'after' => $newData,
            ]),
        ]);

        return response()->json(['message' => 'Expense updated', 'data' => $expense]);
    }

    // DELETE /api/expenses/{id}
    public function destroy($id)
    {
        $expense = Expense::where('company_id', Auth::user()->company_id)->findOrFail($id);

        $oldData = $expense->toArray();

        $expense->delete();

        AuditLog::create([
            'user_id' => Auth::id(),
            'company_id' => Auth::user()->company_id,
            'action' => 'deleted expense',
            'changes' => json_encode([
                'before' => $oldData,
                'after' => null,
            ]),
        ]);

        return response()->json(['message' => 'Expense deleted']);
    }
}
