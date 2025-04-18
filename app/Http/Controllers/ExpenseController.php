<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Generate a cache key that can uniquely identify the query results
            $cacheKey = 'expenses_' . auth()->user()->company_id . '_search_' . $request->search . '_limit_' . ($request->limit ?? 10) . '_page_' . ($request->page ?? 1);

            // Check if the data is cached, otherwise query the database
            $expenses = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($request) {
                return Expense::with('user')
                    ->where('company_id', auth()->user()->company_id)
                    ->when(
                        $request->search,
                        fn($q) =>
                        $q->where('title', 'like', "%$request->search%")
                            ->orWhere('category', 'like', "%$request->search%")
                    )
                    ->paginate($request->limit ?? 10);
            });

            return ResponseHelper::success($expenses, 'Expense fetched successfully', 200);
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to fetch expenses', ['error' => $e->getMessage()], 500);
        }
    }


    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0',
                'category' => 'required|string|max:100',
            ]);

            if ($validator->fails()) {
                return ResponseHelper::error('Validation failed',$validator->errors(), 422);
            }

            $expense = auth()->user()->expenses()->create([
                'company_id' => auth()->user()->company_id,
                'title' => $request->title,
                'amount' => $request->amount,
                'category' => $request->category,
            ]);

            $cacheKey = 'expenses_' . auth()->user()->company_id . '_search_' . $request->search . '_limit_' . ($request->limit ?? 10) . '_page_' . ($request->page ?? 1);
            Cache::forget($cacheKey);

            return ResponseHelper::success($expense, 'Expense stored successfully', 201);
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to store expense', ['error' => $e->getMessage()], 500);
        }
    }

    public function update(Expense $expense, Request $request)
    {
        try {
            $this->authorize('update', $expense);

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0',
                'category' => 'required|string|max:100',
            ]);

            if ($validator->fails()) {
                return ResponseHelper::error('Validation failed',$validator->errors(), 422);
            }

            $old = $expense->toArray();
            $expense->update([
                'title' => $request->title,
                'amount' => $request->amount,
                'category' => $request->category,
            ]);

            AuditLog::create([
                'user_id' => auth()->id(),
                'company_id' => auth()->user()->company_id,
                'action' => 'update',
                'changes' => json_encode(['old' => $old, 'new' => $expense])
            ]);

            $cacheKey = 'expenses_' . auth()->user()->company_id . '_search_' . $request->search . '_limit_' . ($request->limit ?? 10) . '_page_' . ($request->page ?? 1);
            Cache::forget($cacheKey);

            return ResponseHelper::success($expense, 'Expense updated successfully', 200);
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to update expense', ['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Expense $expense, Request $request)
    {

        try {
            $this->authorize('delete', $expense);

            AuditLog::create([
                'user_id' => auth()->id(),
                'company_id' => auth()->user()->company_id,
                'action' => 'delete',
                'changes' => json_encode(['deleted' => $expense])
            ]);

            $expense->delete();

            $cacheKey = 'expenses_' . auth()->user()->company_id . '_search_' . $request->search . '_limit_' . ($request->limit ?? 10) . '_page_' . ($request->page ?? 1);
            Cache::forget($cacheKey);

            return ResponseHelper::success(null, 'Expense deleted successfully', 200);
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to delete expense', ['error' => $e->getMessage()], 500);
        }
    }

}
