<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function update(Request $request, $id)
{
    $expense = Expense::findOrFail($id);
    $oldData = $expense->toArray();

    $expense->update($request->all());

    AuditLog::create([
        'user_id' => auth()->id(),
        'company_id' => auth()->user()->company_id,
        'action' => 'update',
        'changes' => json_encode([
            'old' => $oldData,
            'new' => $expense->toArray(),
        ]),
    ]);

    return response()->json($expense);
}
}
