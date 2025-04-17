<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ExpenseController extends Controller
{

    //create expenses
    public function create(Request $request)
{
    $validated = $request->validate([
        'title' => 'required',
        'amount' => 'required|numeric',
        'category' => 'required',
        'date' => 'required|date',
    ]);

    $validated['company_id'] = $request->user()->company_id;

    $expense = Expense::create($validated);

    // Clear the cache for the list of expenses
    Cache::forget('expenses_for_company_' . auth()->user()->company_id);

    return response()->json($expense, 201);
}

    //List Expense
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;
    
        // Check if the expenses for this company are cached
        $cacheKey = 'expenses_for_company_' . $companyId;

      // Cache the result if not already cached
        $expenses = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($request, $companyId) {
        $query = Expense::with(['user', 'company']) // Eager load relationships
            ->where('company_id', $companyId);

        // Apply search filter if provided
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                    ->orWhere('category', 'like', "%$search%");
            });
        }

        // Paginate the results
        return $query->paginate(10);
    });

    return response()->json([$expenses]);
}


public function destroy($id)
{
    $expense = Expense::find($id);

   //check expenses is scoped to users company id
   if (!$expense || $expense->company_id !== $user->company_id) {
     return response()->json(['message' => 'Expense not found'], 404);
    }

    $oldData = $expense->toArray();

    $expense->delete();

    // Clear the cache for the list of expenses
    Cache::forget('expenses_for_company_' . auth()->user()->company_id);

    AuditLog::create([
        'user_id' => auth()->id(),
        'company_id' => auth()->user()->company_id,
        'action' => 'delete',
        'changes' => [
            'before' => $oldData,
            'after' => null,
        ],
    ]);

    return response()->json(['message' => 'Expense deleted']);
}



}
