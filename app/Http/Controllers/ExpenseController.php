<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $expenses = Expense::with('user')
            ->where('company_id', $companyId)
            ->when($request->title, fn($q) => $q->where('title', 'like', "%{$request->title}%"))
            ->when($request->category, fn($q) => $q->where('category', $request->category))
            ->paginate(10);

        return response()->json($expenses);
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'amount' => 'required|numeric',
            'category' => 'required|string',
        ]);

        $data['company_id'] = auth()->user()->company_id;
        $data['user_id'] = auth()->id();

        try {
            $expense = Expense::create($data);

            try {
                $this->clearExpenseCache();
            } catch (\Throwable $cacheException) {
                Log::warning('Cache clear failed after creating expense', [
                    'error' => $cacheException->getMessage()
                ]);
            }

            return response()->json($expense, 201);
        } catch (\Exception $e) {
            Log::error('Error creating expense', [
                'message' => $e->getMessage(),
                'data' => $data,
            ]);

            return response()->json(['error' => 'Failed to create expense'], 500);
        }
    }



    public function update(Request $request, Expense $expense)
    {
        $this->authorizeAccess($expense);

        $data = $request->validate([
            'title' => 'string',
            'amount' => 'numeric',
            'category' => 'string',
        ]);

        try {
            $old = clone $expense;
            $expense->update($data);
            $this->logAudit('updated_expense', $old, $expense);

            try {
                $this->clearExpenseCache();
            } catch (\Throwable $cacheException) {
                Log::warning('Cache clear failed after expense update', [
                    'error' => $cacheException->getMessage()
                ]);
            }

            return response()->json($expense);
        } catch (\Exception $e) {
            Log::error('Error updating expense', [
                'message' => $e->getMessage(),
                'data' => $data,
            ]);

            return response()->json(['error' => 'Failed to update expense'], 500);
        }
    }


    public function destroy(Expense $expense)
    {
        $this->authorizeAccess($expense);

        try {
            $this->logAudit('deleted_expense', $expense, null);
            $expense->delete();

            try {
                $this->clearExpenseCache();
            } catch (\Throwable $cacheException) {
                Log::warning('Cache clear failed after expense deletion', [
                    'error' => $cacheException->getMessage()
                ]);
            }

            return response()->json(['message' => 'Deleted']);
        } catch (\Exception $e) {
            Log::error('Error deleting expense', [
                'message' => $e->getMessage(),
                'expense_id' => $expense->id,
            ]);

            return response()->json(['error' => 'Failed to delete expense'], 500);
        }
    }


    protected function authorizeAccess($expense)
    {   
        if ($expense->company_id !== auth()->user()->company_id) {
            abort(403, 'Unauthorized access');
        }
    }

    protected function logAudit($action, $old, $new)
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'company_id' => auth()->user()->company_id,
            'action' => $action,
            'changes' => json_encode([
                'old' => $old?->toArray(),
                'new' => $new?->toArray(),
            ])
        ]);
    }

    protected function clearExpenseCache()
    {
        $companyId = auth()->user()->company_id;
        Cache::flush();
    }
}
