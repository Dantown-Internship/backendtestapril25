<?php

namespace App\Http\Controllers;

use App\Repositories\ExpenseRepository;
use App\Http\Requests\Expense\CreateRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Models\Expense;

class ExpenseController extends Controller
{
    public function __construct(
        protected ExpenseRepository $expenseRepository
    ) {}
    
    public function index(Request $request): JsonResponse
    {
        $expenses = $this->expenseRepository->getCompanyExpenses(
            $request->user()->company_id,
            $request->search
        );
        
        return $this->respond('Expenses retrieved', $expenses->toArray());
    }
    
    public function store(CreateRequest $request): JsonResponse
    {   

        $expense = $this->expenseRepository->createExpense($request);
        
        return $this->respond('Expense created successfully', $expense->toArray(), statusCode:Response::HTTP_CREATED);
    }
    
    public function update(createRequest $request, Expense $expense): JsonResponse
    {
        $updatedExpense = $this->expenseRepository->updateExpense(
            $expense,
            $request->only(['title', 'amount', 'category'])
        );
        
        return $this->respond('Expense updated', $updatedExpense->toArray());
    }
    
    public function destroy(Expense $expense): JsonResponse
    {
        $this->expenseRepository->deleteExpense($expense);
        return $this->respond('Expense deleted');
    }
}
