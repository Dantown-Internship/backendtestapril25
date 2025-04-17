<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expense;


class ExpenseController extends Controller
{

    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $search = $request->input('search');
        $page = $request->input('page', 1);

        // Cache key can include search and page for specificity
        $cacheKey = "expenses:company:{$companyId}:search:" . md5($search) . ":page:{$page}";

        $expenses = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($companyId, $search) {
            $query = Expense::with(['user', 'company']) // if needed
                ->where('company_id', $companyId);

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
                });
            }

            return $query->paginate(10);
        });

        return $expenses;
    }

    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required',
            'amount' => 'required|numeric',
            'category' => 'required',
        ]);
    
        $validated['user_id'] = auth()->id();
        $validated['company_id'] = auth()->user()->company_id;
    
        return Expense::create($validated);
    }
    
    public function update(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);
    
        if ($expense->company_id !== auth()->user()->company_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
    
        $expense->update($request->only(['title', 'amount', 'category']));
    
        return $expense;
    }
    
    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);
    
        if ($expense->company_id !== auth()->user()->company_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
    
        $expense->delete();
    
        return response()->json(['message' => 'Expense deleted']);
    }
    
}
