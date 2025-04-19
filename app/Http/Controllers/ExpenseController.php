<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Services\ExpenseService;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    protected $expenseService;

    public function __construct(ExpenseService $expenseService)
    {
        $this->expenseService = $expenseService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $expenses = $this->expenseService->getAllExpenses(request());
        return response()->json($expenses);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->expenseService->createExpense($request);
        return response()->json([
            'message' => 'Expense created successfully'],
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Expense $expense)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Expense $expense)
    {
        $this->authorizeExpense($expense);
        $this->expenseService->updateExpense($request, $expense);
        return response()->json([
            'message' => 'Expense updated successfully'],
            200
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        $this->authorizeExpense($expense);
        $this->expenseService->deleteExpense($expense);
        return response()->json([
            'message' => 'Expense deleted successfully'],
            200
        );
    }

    private function authorizeExpense(Expense $expense)
    {
        if ($expense->company_id !== auth()->user()->company_id) {
            abort(403, 'Unauthorized');
        }
    }
}
