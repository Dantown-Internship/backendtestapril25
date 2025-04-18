<?php

namespace App\Http\Controllers;

use App\Models\Expenses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\AuditLog;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;


class ExpensesController extends Controller
{
   
    public function saveExpenses(Request $request)
    {
        try {
            Log::info('Starting expense creation process.', [
                'requested_by' => Auth::id(),
                'input' => $request->only(['title', 'amount', 'category'])
            ]);
    
            $request->validate([
                'title' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0',
                'category' => 'required|string|max:255',
            ]);
    
            Log::info('Validation passed for expense creation.');
    
            $user = Auth::user();
    
            $expense = Expenses::create([
                'company_id' => $user->company_id,
                'user_id' => $user->id,
                'title' => $request->title,
                'amount' => $request->amount,
                'category' => $request->category,
            ]);
    
            Log::info('Expense created successfully.', [
                'expense_id' => $expense->id,
                'created_by' => $user->id
            ]);
    
            return $this->successResponse('Expense created successfully', $expense, 201);
    
        } catch (ValidationException $e) {
            Log::warning('Validation failed during expense creation.', ['errors' => $e->errors()]);
            return $this->errorResponse($e->getMessage(), 422, $e->errors());
    
        } catch (\Exception $e) {
            Log::error('Exception during expense creation.', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse('Failed to create expense', 500);
        }
    }


    public function listExpenses(Request $request)
{
    try {
        $user = Auth::user();

        Log::info('Starting expense listing.', [
            'requested_by' => $user->id,
            'company_id' => $user->company_id,
            'search_query' => $request->search ?? 'none'
        ]);

        $query = Expenses::where('company_id', $user->company_id)
            ->with(['user'])
            ->when($request->search, function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('category', 'like', '%' . $request->search . '%');
            });

        $cacheKey = 'expenses_' . $user->company_id . '_' . md5($request->fullUrl());
        Log::debug('Generated cache key for expense listing.', ['cache_key' => $cacheKey]);

        $expenses = Cache::remember($cacheKey, 600, function () use ($query) {
            Log::info('Cache miss — querying database for expenses.');
            return $query->paginate(10);
        });

        Log::info('Expenses retrieved successfully.', ['count' => $expenses->count()]);

        return $this->successResponse('Expenses retrieved successfully', $expenses);
    } catch (\Exception $e) {
        Log::error('Failed to retrieve expenses.', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return $this->errorResponse('Failed to retrieve expenses', 500);
    }
}



    public function updateExpenses(Request $request, $id)
    {
        try {
            $request->validate([
                'title' => 'string|max:255',
                'amount' => 'numeric|min:0',
                'category' => 'string|max:255',
            ]);

            $user = Auth::user();
            $expense = Expenses::where('company_id', $user->company_id)->findOrFail($id);

            $oldValues = $expense->only(['title', 'amount', 'category']);
            $expense->update($request->only(['title', 'amount', 'category']));
            $newValues = $expense->only(['title', 'amount', 'category']);

            // Log the update
            AuditLog::create([
                'user_id' => $user->id,
                'company_id' => $user->company_id,
                'action' => 'expense_updated',
                'changes' => ['old' => $oldValues, 'new' => $newValues],
            ]);

            return $this->successResponse('Expense updated successfully', $expense);
        } catch (ValidationException $e) {
            return $this->errorResponse($e->getMessage(), 422, $e->errors());
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse('Expense not found', 404);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update expense', 500);
        }
    }

    public function destroyExpenses($id)
    {
        try {
            $user = Auth::user();
            $expense = Expenses::where('company_id', $user->company_id)->findOrFail($id);

            // Log the deletion
            AuditLog::create([
                'user_id' => $user->id,
                'company_id' => $user->company_id,
                'action' => 'expense_deleted',
                'changes' => $expense->toArray(),
            ]);

            $expense->delete();

            return $this->successResponse('Expense deleted successfully');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse('Expense not found', 404);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete expense', 500);
        }
    }

    protected function successResponse(string $message, $data = [], int $status = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $status);
    }

    protected function errorResponse(string $message, int $status, array $errors = [])
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors
        ], $status);
    }
}