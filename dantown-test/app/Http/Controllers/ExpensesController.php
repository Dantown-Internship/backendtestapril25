<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ExpenseService;

class ExpensesController extends Controller
{
    protected $expenseService;

    public function __construct(ExpenseService $expenseService)
    {
        $this->expenseService = $expenseService;
    }

    public function update(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);
        $oldExpense = $expense->replicate();
        $expense->update($request->all());

        // Log the changes
        AuditLog::create([
            'user_id' => auth()->id(),
            'company_id' => auth()->user()->company_id,
            'action' => 'update',
            'changes' => json_encode(['old' => $oldExpense, 'new' => $expense])
        ]);

        return response()->json($expense);
    }
}
