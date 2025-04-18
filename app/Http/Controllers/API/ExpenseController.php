<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Models\Expense;
use App\Services\ExpenseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    protected ExpenseService $expenseService;


    public function __construct(ExpenseService $expenseService)
    {
        $this->expenseService = $expenseService;
    }

    
    public function index(Request $request): JsonResponse
    {
        $filters = [
            'search' => $request->get('search'),
            'category' => $request->get('category'),
            'page' => $request->get('page', 1),
            'per_page' => $request->get('per_page', 15),
        ];

        $expenses = $this->expenseService->getExpenses(
            $request->user()->company_id,
            $filters
        );

        return response()->json([
            'data' => $expenses->items(),
            'meta' => [
                'current_page' => $expenses->currentPage(),
                'last_page' => $expenses->lastPage(),
                'per_page' => $expenses->perPage(),
                'total' => $expenses->total(),
            ],
        ]);
    }

 
    public function store(StoreExpenseRequest $request): JsonResponse
    {
        $expense = Expense::create($request->validated());

        $this->expenseService->clearExpenseCache($request->user()->company_id);

        return response()->json([
            'message' => 'Expense created successfully',
            'data' => $expense,
        ], 201);
    }

    
    public function show(Request $request, Expense $expense): JsonResponse
    {
        if ($expense->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Expense not found.'], 404);
        }
        
        $expense->load('user');

        return response()->json([
            'data' => $expense,
        ]);
    }

   
    public function update(UpdateExpenseRequest $request, Expense $expense): JsonResponse
    {
        if ($expense->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Expense not found.'], 404);
        }
        
        if (!$request->user()->isAdmin() && !$request->user()->isManager()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }
        
        $expense->update($request->validated());

        $this->expenseService->clearExpenseCache($request->user()->company_id);

        return response()->json([
            'message' => 'Expense updated successfully',
            'data' => $expense->fresh(),
        ]);
    }

   
    public function destroy(Request $request, Expense $expense): JsonResponse
    {
        if ($expense->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Expense not found.'], 404);
        }
        
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }
        
        $expense->delete();

        $this->expenseService->clearExpenseCache($request->user()->company_id);

        return response()->json([
            'message' => 'Expense deleted successfully',
        ]);
    }
}
