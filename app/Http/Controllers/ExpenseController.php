<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

use App\Services\ExpenseService;

use Illuminate\Support\Facades\Cache;
use App\Http\Resources\ExpenseResource;
use App\Http\Requests\Expense\StoreExpenseRequest;

use App\Http\Requests\Expense\UpdateExpenseRequest;
use App\Services\Interfaces\ExpenseServiceInterface;

class ExpenseController extends Controller
{
    public function __construct(
        protected ExpenseServiceInterface $expenseService
    ) {}

    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $cacheKey = "expenses_company_{$companyId}_" . md5(json_encode($request->query()));
    
        $expenses = Cache::remember($cacheKey, 60, function () use ($request, $companyId) {
            return Expense::with(['user', 'company'])
                ->where('company_id', $companyId)
                ->when($request->title, fn($q) => $q->where('title', 'like', '%' . $request->title . '%'))
                ->when($request->category, fn($q) => $q->where('category', $request->category))
                ->latest()
                ->paginate(10);
        });
    
        return ExpenseResource::collection($expenses);
    }
    
    public function store(StoreExpenseRequest $request)
    {
        $expense = $this->expenseService->createExpense([
            ...$request->validated(),
            'user_id' => auth()->id(),
            'company_id' => auth()->user()->company_id,
        ]);

        return new ExpenseResource($expense);
    }
    
    public function update(UpdateExpenseRequest $request, Expense $expense)
    {
        $this->expenseService->updateExpense($expense, $request->validated());
        return response()->json(['message' => 'Expense updated']);
    }
    
    public function destroy(Expense $expense)
    {
        $this->expenseService->deleteExpense($expense);    
        return response()->json(['message' => 'Expense deleted']);
    }
    
}

