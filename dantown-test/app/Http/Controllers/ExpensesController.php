<?php

namespace App\Http\Controllers;

use App\Services\ExpenseService;
use App\Http\Controllers\Controller;
use App\Http\Resources\ExpenseResource;
use App\Models\Expense;
use Illuminate\Http\Request;

use Auth;

class ExpensesController extends Controller
{
    protected $expenseService;

    public function __construct(ExpenseService $expenseService)
    {
        $this->expenseService = $expenseService;
    }


       /**
     * Display a listing of the expenses.
     */
    public function index(Request $request)
    {
        $authUser = Auth::user()->load('company');
        
        $perPage = $request->input('per_page', 10);
        
        $expenses = $this->expenseService->showAllExpenses2($authUser,$perPage);
        return response()->json($expenses, 200);
    }

    /**
     * Store a newly created expense in storage.
     */
    public function store(StoreExpenseRequest $request)
    {
        $authUser = Auth::user()->load('company');
        $expenseRequest = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1.00',
        ]);
       
        $expense = $this->expenseService->createExpense($authUser, $expenseRequest);
        
        return response()->json($expense, 200);
        
    }


    public function show(int $id)
    {
        $authUser = Auth::user()->load('company');

        $expense = $this->expenseService->getExpenseById($authUser, $id);
        if ($expense->company_id !== Auth::user()->company_id) {
            return response()->json(['success'=>false, 'message' => 'Forbidden', 'data'=> []], 403);
        }
       return response()->json($expense, 200);
    }

    /**
     * Remove the specified expense from storage.
     */
    public function destroy($id)
    {
        $this->authorize('delete', $expense); // Use the ExpensePolicy

        $expense->delete();

        return response()->json(['message' => 'Expense deleted successfully']);
    }



    public function update(Request $request, $id)
    {
        $authUser = Auth::user()->load('company');

        $updateRequest = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
        ]);
        $expense = $this->expenseService->updateExpense($authUser, $id, $updateRequest);

        return response()->json($expense);
    }
}
