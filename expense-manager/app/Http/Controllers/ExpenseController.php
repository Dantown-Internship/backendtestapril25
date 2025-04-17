<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{

    //create expenses
    public function create(Request $request)
    {
        $request->validate([
            'title'=>'required',
            'amount'=>'required|numeric',
            'category'=>'required',
            'company_id'=>'required|exists:companies,id'
        ]);

        $expense = Expense::create([
            'title' => $request->title,
            'amount' => $request->amount,
            'category' => $request->category,
            'company_id' => $request->company_id,
            'user_id' => auth()->user()->id, 
        ]);
    
        return response()->json(['expense' => $expense], 201);
    }

    //List Expense
    public function index(Request $request)
    {
        $expenses = Expense::where('company_id', $request->user()->company_id)
                    ->when($request->search, function ($query) use ($request) {
                        $query->where('title', 'like', "%{$request->search}%")
                            ->orWhere('category', 'like', "%{$request->search}%");
                    })
                    ->paginate(10);

        return response()->json($expenses);
    }

    //update expense
    public function update(Request $request, $id)
{
    
    $expense = Expense::find($id);
    if (!$expense) {
        return response()->json(['message' => 'Expense not found'], 404);
    }

    $oldData = $expense->toArray();
    $data = $request->only(['title', 'amount', 'category', 'date']);
    $expense->update($data);

    $newData = $expense->fresh()->toArray();

    AuditLog::create([
        'user_id' => auth()->id(),
        'company_id' => auth()->user()->company_id,
        'action' => 'update',
        'changes' => [
            'before' => $oldData,
            'after' => $newData,
        ],
    ]);

    return response()->json(['expense' => $expense]);
}


public function destroy($id)
{
    $expense = Expense::find($id);

    if (!$expense) {
        return response()->json(['message' => 'Expense not found'], 404);
    }

    $oldData = $expense->toArray();

    $expense->delete();

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
