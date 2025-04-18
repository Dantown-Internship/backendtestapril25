<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {

        $cacheKey = 'expenses:' . auth()->id() . ':' . request('page', 1) . ':' . request('search', '');

        $expenses = Cache::remember($cacheKey, 60, function () use ($request) {
            return Expense::with('user')
                ->where('company_id', auth()->user()->company_id)
                ->when($request->search, function ($query) use ($request) {
                    $query->where(function ($q) use ($request) {
                        $q->where('title', 'like', '%' . $request->search . '%')
                            ->orWhere('category', 'like', '%' . $request->search . '%');
                    });
                })
                ->paginate(10);
        });

        return response()->json($expenses);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'title' => 'required|string',
            'amount' => 'required|numeric',
            'category' => 'required|string',
        ]);

        $expense = Expense::create([
            'title' => $request->title,
            'amount' => $request->amount,
            'category' => $request->category,
            'user_id' => auth()->id(),
            'company_id' => auth()->user()->company_id,
        ]);
        // Clear cache
        $this->clearExpenseCache($user);

        return response()->json(['message' => 'Expense created successfully', 'data' => $expense], 201);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();

        $expense = Expense::where('id', $id)
            ->where('company_id', auth()->user()->company_id)
            ->firstOrFail();

        $this->authorizeRole(['Admin', 'Manager']);

        $old = $expense->toArray();

        $expense->update($request->only(['title', 'amount', 'category']));

        $new = $expense->fresh()->toArray();

        // Log changes
        AuditLog::create([
            'user_id' => auth()->id(),
            'company_id' => auth()->user()->company_id,
            'action' => 'updated',
            'changes' => json_encode(['old' => $old, 'new' => $expense->toArray()]),
        ]);

        $this->clearExpenseCache($user);

        return response()->json(['message' => 'Expense updated successfully']);
    }

    public function destroy($id)
    {
        $user = auth()->user();

        $this->authorizeRole(['Admin']);

        $expense = Expense::where('id', $id)
            ->where('company_id', auth()->user()->company_id)
            ->firstOrFail();

        // Log deletion
        AuditLog::create([
            'user_id' => auth()->id(),
            'company_id' => auth()->user()->company_id,
            'action' => 'deleted',
            'changes' => json_encode(['deleted' => $expense->toArray()]),
        ]);

        $expense->delete();

        // Clear cache
        $this->clearExpenseCache($user);

        return response()->json(['message' => 'Expense deleted successfully']);
    }

    protected function clearExpenseCache($user)
    {
        $searches = ['', 'food', 'transport', 'office']; // any categories or common searches you want to clear
        foreach ($searches as $search) {
            for ($page = 1; $page <= 5; $page++) {
                $key = "expenses:{$user->id}:{$page}:{$search}";
                Cache::forget($key);
            }
        }
    }
}
