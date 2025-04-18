<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuditLogResource;
use App\Http\Resources\ExpenseResource;
use App\Models\AuditLog;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    /**
     * Display a listing of expenses for the authenticated user's company.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $limit = $request->query('limit', 10); // Default to 10 items per page
        $limit = min(max((int)$limit, 1), 100); // Ensure limit is between 1 and 100

        // Create a unique cache key based on user and request parameters
        $cacheKey = 'expenses_' . $user->id . '_' . $user->company_id . '_' . md5(json_encode($request->all()));

        // Get data from cache or run the query
        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($request, $user, $limit) {
            $query = Expense::with('user')
                ->where('company_id', $user->company_id);

            // Filter by user if not admin/manager (employees can only see their own expenses)
            if ($user->isEmployee()) {
                $query->where('user_id', $user->id);
            }

            // Handle optional filters
            if ($request->has('category')) {
                $query->where('category', $request->category);
            }

            // Handle search term
            if ($request->has('search')) {
                $query->where(function ($q) use ($request) {
                    $q->where('title', 'like', '%' . $request->search . '%')
                      ->orWhere('category', 'like', '%' . $request->search . '%');
                });
            }

            // Handle sorting
            $sortField = $request->sort_by ?? 'created_at';
            $sortDirection = $request->sort_direction ?? 'desc';
            $query->orderBy($sortField, $sortDirection);

            $expenses = $query->paginate($limit, ['*'], 'page', $request->query('page'))->toResourceCollection();

            return ExpenseResource::collection($expenses);
        });
    }

    /**
     * Store a newly created expense.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string|max:100',
        ]);

        // Use a transaction to ensure both expense and audit log are created
        return DB::transaction(function () use ($request, $user) {
            // Create expense
            $expense = Expense::create([
                'title' => $request->title,
                'amount' => $request->amount,
                'category' => $request->category,
                'user_id' => $user->id,
                'company_id' => $user->company_id,
            ]);

            // Create audit log
            AuditLog::create([
                'user_id' => $user->id,
                'company_id' => $user->company_id,
                'action' => 'create',
                'changes' => json_encode([
                    'expense_id' => $expense->id,
                    'old' => null,
                    'new' => $expense->toArray(),
                ]),
            ]);

            // Clear list caches with a pattern
            $this->clearExpenseListCache($user);

            return response()->json([
                'message' => 'Expense created successfully',
                'expense' => new ExpenseResource($expense->load('user')),
            ], 201);
        });
    }

    /**
     * Display the specified expense.
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();

        // Create a unique cache key for this specific expense
        $cacheKey = 'expense_' . $id . '_' . $user->id;

        return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($user, $id) {
            $expense = Expense::with('user')
                ->where('id', $id)
                ->where('company_id', $user->company_id)
                ->first();

            if (!$expense) {
                return response()->json(['message' => 'Expense not found'], 404);
            }

            // Check if user is authorized to view the expense
            if ($user->isEmployee() && $expense->user_id !== $user->id) {
                return response()->json(['message' => 'Unauthorized to view this expense'], 403);
            }

            return response()->json([
                'expense' => new ExpenseResource($expense)
            ]);
        });
    }

    /**
     * Update the specified expense.
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();

        $expense = Expense::where('id', $id)
            ->where('company_id', $user->company_id)
            ->first();

        if (!$expense) {
            return response()->json(['message' => 'Expense not found'], 404);
        }

        // Check if user is authorized to update the expense
        // Employees can only update their own expenses, managers and admins can update any
        if ($user->isEmployee() && $expense->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized to update this expense'], 403);
        }

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'amount' => 'sometimes|required|numeric|min:0',
            'category' => 'sometimes|required|string|max:100',
        ]);

        // Use a transaction to ensure both expense and audit log are updated
        return DB::transaction(function () use ($request, $user, $expense, $id) {
            // Store old values for audit log
            $oldValues = $expense->toArray();

            // Update expense
            $expense->title = $request->title ?? $expense->title;
            $expense->amount = $request->amount ?? $expense->amount;
            $expense->category = $request->category ?? $expense->category;
            $expense->save();

            // Create audit log
            AuditLog::create([
                'user_id' => $user->id,
                'company_id' => $user->company_id,
                'action' => 'update',
                'changes' => json_encode([
                    'expense_id' => $expense->id,
                    'old' => $oldValues,
                    'new' => $expense->toArray(),
                ]),
            ]);

            // Forget cache for this expense to ensure fresh data
            Cache::forget('expense_' . $id . '_' . $user->id);

            // Clear list caches with a pattern
            $this->clearExpenseListCache($user);

            return response()->json([
                'message' => 'Expense updated successfully',
                'expense' => new ExpenseResource($expense->load('user')),
            ]);
        });
    }

    /**
     * Remove the specified expense.
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        $expense = Expense::where('id', $id)
            ->where('company_id', $user->company_id)
            ->first();

        if (!$expense) {
            return response()->json(['message' => 'Expense not found'], 404);
        }

        // Check if user is authorized to delete the expense
        // Employees can only delete their own expenses, managers and admins can delete any
        if ($user->isEmployee() && $expense->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized to delete this expense'], 403);
        }

        // Use a transaction to ensure both expense and audit log are handled correctly
        return DB::transaction(function () use ($user, $expense, $id) {
            // Store values for audit log
            $oldValues = $expense->toArray();

            // Delete expense
            $expense->delete();

            // Create audit log
            AuditLog::create([
                'user_id' => $user->id,
                'company_id' => $user->company_id,
                'action' => 'delete',
                'changes' => json_encode([
                    'expense_id' => $expense->id,
                    'old' => $oldValues,
                    'new' => null,
                ]),
            ]);

            // Forget cache for this expense
            Cache::forget('expense_' . $id . '_' . $user->id);

            // Clear list caches with a pattern
            $this->clearExpenseListCache($user);

            return response()->json([
                'message' => 'Expense deleted successfully',
                'status' => 'success'
            ]);
        });
    }

    /**
     * Get audit logs for a specific expense.
     */
    public function auditLogs(Request $request, $id)
    {
        $user = $request->user();

        // Only managers and admins can view audit logs
        if ($user->isEmployee()) {
            return response()->json(['message' => 'Unauthorized to view audit logs'], 403);
        }

        $expense = Expense::where('id', $id)
            ->where('company_id', $user->company_id)
            ->first();

        if (!$expense) {
            return response()->json(['message' => 'Expense not found'], 404);
        }

        $auditLogs = AuditLog::with('user')
            ->where('company_id', $user->company_id)
            ->where('changes', 'like', '%"expense_id":' . $expense->id . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return AuditLogResource::collection($auditLogs);
    }

    /**
     * Helper to clear expense list cache after updates and deletes
     */
    private function clearExpenseListCache($user)
    {
        // Clear all cached expense lists associated with this user's company
        $pattern = 'expenses_*_' . $user->company_id . '_*';
        foreach (Cache::get($pattern, []) as $key) {
            Cache::forget($key);
        }
    }
}
