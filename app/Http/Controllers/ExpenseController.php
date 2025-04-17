<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ExpenseController extends Controller
{

    public function index(Request $request)
    {
        $query = Expense::with(['user', 'company'])
            ->where('company_id', auth()->user()->company_id);
    
        if ($request->has('search') && $search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }
    
        // Directly fetch expenses WITHOUT caching
        $expenses = $query->paginate(10);
    
        return response()->json([
            'status' => true,
            'data' => $expenses
        ]);
    }
    

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'amount' => 'required|numeric',
            'category' => 'required|string'
        ]);

        $expense = Expense::create([
            'company_id' => auth()->user()->company_id,
            'user_id' => auth()->id(),
            ...$data
        ]);

        Log::info('Expense created', ['expense_id' => $expense->id]);
        return response()->json(['status'=>true,'data'=>$expense], 201);
    }


    



    public function update(Request $request, Expense $expense)
    {
        if ($expense->company_id !== auth()->user()->company_id) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string|max:100'
        ]);

        $oldValues = [
            'title' => $expense->title,
            'amount' => $expense->amount,
            'category' => $expense->category
        ];

        $expense->update([
            'title' => $data['title'],
            'amount' => $data['amount'],
            'category' => $data['category']
        ]);

        $newValues = [
            'title' => $expense->title,
            'amount' => $expense->amount,
            'category' => $expense->category
        ];

        AuditLog::create([
            'user_id' => auth()->id(),
            'company_id' => auth()->user()->company_id,
            'action' => 'update',
            'changes' => [
                'expense_id' => $expense->id,
                'old' => $oldValues,
                'new' => $newValues
            ]
        ]);

        Log::info('Expense updated', [
            'expense_id' => $expense->id,
            'user_id' => auth()->id()
        ]);

        return response()->json([
            'status' => true,
            'data' => $expense
        ]);
    }

    public function destroy(Expense $expense)
    {
        if ($expense->company_id !== auth()->user()->company_id) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $values = [
            'title' => $expense->title,
            'amount' => $expense->amount,
            'category' => $expense->category
        ];

        AuditLog::create([
            'user_id' => auth()->id(),
            'company_id' => auth()->user()->company_id,
            'action' => 'delete',
            'changes' => [
                'expense_id' => $expense->id,
                'values' => $values
            ]
        ]);

        $expense->delete();

        Log::info('Expense deleted', [
            'expense_id' => $expense->id,
            'user_id' => auth()->id()
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Expense deleted successfully'
        ]);
    }

    
}