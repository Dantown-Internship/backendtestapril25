<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Services\ExpenseCacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    protected $cacheService;

    public function __construct(ExpenseCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function index(Request $request)
    {
        $filters = [
            'search' => $request->search,
            'sort_by_amount' => $request->sort_by_amount,
        ];

        $expenses = $this->cacheService->getCachedExpenses(
            $request->user()->company_id,
            $filters
        );

        return response()->json($expenses);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
        ]);

        $expense = Expense::create([
            'title' => $request->title,
            'amount' => $request->amount,
            'category' => $request->category,
            'company_id' => $request->user()->company_id,
            'user_id' => $request->user()->id,
        ]);

        // Invalidate cache for this company
        $this->cacheService->invalidateCompanyExpenses($request->user()->company_id);

        return response()->json($expense->load(['user:id,name', 'company:id,name']), 201);
    }

    public function update(Request $request, Expense $expense)
    {
        // Ensure the expense belongs to the user's company
        if ($expense->company_id !== $request->user()->company_id) {
            abort(403, 'Unauthorized to update this expense');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
        ]);

        $expense->update([
            'title' => $request->title,
            'amount' => $request->amount,
            'category' => $request->category,
        ]);

        // Invalidate cache for this company
        $this->cacheService->invalidateCompanyExpenses($request->user()->company_id);

        return response()->json($expense->load(['user:id,name', 'company:id,name']));
    }

    public function destroy(Request $request, Expense $expense)
    {
        // Ensure the expense belongs to the user's company
        if ($expense->company_id !== $request->user()->company_id) {
            abort(403, 'Unauthorized to delete this expense');
        }

        $expense->delete();

        // Invalidate cache for this company
        $this->cacheService->invalidateCompanyExpenses($request->user()->company_id);

        return response()->json(null, 204);
    }
}