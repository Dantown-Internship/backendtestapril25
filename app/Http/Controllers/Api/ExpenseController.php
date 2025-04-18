<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\AuditLog;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ExpenseController;
use Illuminate\Support\Facades\DB;
use App\Exceptions\UnauthorizedAccessException;

class ExpenseController extends Controller
{
    use ApiResponse;

    public function getExpenses(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string',
            'category' => 'nullable|string',
            'min_amount' => 'nullable|numeric',
            'max_amount' => 'nullable|numeric',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'sort_by' => 'nullable|in:title,amount,category,created_at',
            'sort_direction' => 'nullable|in:asc,desc',
            'per_page' => 'nullable|integer|min:1|max:100',
            'user_id' => 'nullable|uuid|exists:users,id',
        ]);

        $query = Expense::query()
            ->where('company_id', $request->user()->company_id)
            ->with(['user:id,name,email']); // Eager load user relationship

        // Search in title and category
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('category', 'like', "%{$request->search}%");
            });
        }

        // Category filter
        if ($request->category) {
            $query->where('category', $request->category);
        }

        // Amount range filter
        if ($request->min_amount) {
            $query->where('amount', '>=', $request->min_amount);
        }
        if ($request->max_amount) {
            $query->where('amount', '<=', $request->max_amount);
        }

        // Date range filter
        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // User filter
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Sorting
        $sortBy = $request->sort_by ?? 'created_at';
        $sortDirection = $request->sort_direction ?? 'desc';
        $query->orderBy($sortBy, $sortDirection);

        // Get categories for metadata
        $categories = Expense::where('company_id', $request->user()->company_id)
            ->select('category')
            ->distinct()
            ->pluck('category');

        // Get summary statistics
        $statistics = [
            'total_amount' => $query->sum('amount'),
            'average_amount' => $query->avg('amount'),
            'total_count' => $query->count(),
            'by_category' => DB::table('expenses')
                ->where('company_id', $request->user()->company_id)
                ->select('category', 
                    DB::raw('COUNT(*) as count'), 
                    DB::raw('SUM(amount) as total_amount'),
                    DB::raw('AVG(amount) as average_amount'))
                ->groupBy('category')
                ->get()
        ];

        // Pagination
        $perPage = $request->per_page ?? 15;
        $expenses = $query->paginate($perPage);

        // Descriptive message based on the filters applied
        $message = 'Expenses retrieved successfully';
        
        //filter details for message
        $appliedFilters = [];
        if ($request->search) $appliedFilters[] = "search: '{$request->search}'";
        if ($request->category) $appliedFilters[] = "category: '{$request->category}'";
        if ($request->min_amount) $appliedFilters[] = "min amount: {$request->min_amount}";
        if ($request->max_amount) $appliedFilters[] = "max amount: {$request->max_amount}";
        if ($request->start_date) $appliedFilters[] = "from: {$request->start_date}";
        if ($request->end_date) $appliedFilters[] = "to: {$request->end_date}";
        
        if (!empty($appliedFilters)) {
            $message .= ' (Filtered by: ' . implode(', ', $appliedFilters) . ')';
        }

        return $this->success([
            'expenses' => $expenses,
            'metadata' => [
                'categories' => $categories,
                'statistics' => $statistics,
                'filters' => [
                    'available_sort_fields' => ['title', 'amount', 'category', 'created_at'],
                    'available_sort_directions' => ['asc', 'desc'],
                ],
            ]
        ], $message);
    }

    public function createExpense(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'category' => ['required', 'string', 'max:255'],
        ]);

        $expense = Expense::create([
            'title' => $request->title,
            'amount' => $request->amount,
            'category' => $request->category,
            'company_id' => $request->user()->company_id,
            'user_id' => $request->user()->id,
        ]);

        // Create audit log
        AuditLog::create([
            'user_id' => $request->user()->id,
            'company_id' => $request->user()->company_id,
            'action' => 'created',
            'changes' => json_encode([
                'expense_id' => $expense->id,
                'new_values' => $expense->toArray()
            ])
        ]);

        return $this->success($expense, 'Expense created successfully', 201);
    }

    public function updateExpense(Request $request, $expenseId)
    {
        $expense = Expense::where('company_id', $request->user()->company_id)
            ->find($expenseId);

        if (!$expense) {
            throw new UnauthorizedAccessException(
                'Resource not found or you do not have permission to access it'
            );
        }

        $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'amount' => ['sometimes', 'numeric', 'min:0'],
            'category' => ['sometimes', 'string', 'max:255'],
        ]);

        $oldValues = $expense->toArray();
        $expense->update($request->only(['title', 'amount', 'category']));

        // Create audit log
        AuditLog::create([
            'user_id' => $request->user()->id,
            'company_id' => $request->user()->company_id,
            'action' => 'updated',
            'changes' => json_encode([
                'expense_id' => $expense->id,
                'old_values' => $oldValues,
                'new_values' => $expense->toArray()
            ])
        ]);

        return $this->success($expense, 'Expense updated successfully');
    }

    public function deleteExpense(Request $request, $expenseId)
    {
        $expense = Expense::where('company_id', $request->user()->company_id)
            ->find($expenseId);

        if (!$expense) {
            throw new UnauthorizedAccessException(
                'Resource not found or you do not have permission to access it'
            );
        }

        $oldValues = $expense->toArray();
        $expense->delete();

        // Create audit log
        AuditLog::create([
            'user_id' => $request->user()->id,
            'company_id' => $request->user()->company_id,
            'action' => 'deleted',
            'changes' => json_encode([
                'expense_id' => $expenseId,
                'deleted_values' => $oldValues
            ])
        ]);

        return $this->success($expenseId, 'Expense deleted successfully');
    }
    
} 