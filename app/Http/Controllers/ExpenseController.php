<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseIndexRequest;
use App\Http\Requests\ExpenseStoreRequest;
use App\Http\Requests\ExpenseUpdateRequest;
use App\Models\Expense;
use App\Services\ExpenseReportService;
use App\Traits\JsonResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * Expense Controller
 *
 * Handles all expense-related operations including:
 * - Listing expenses with filtering and pagination
 * - Creating new expenses
 * - Viewing expense details
 * - Updating existing expenses
 * - Deleting expenses
 * - Generating expense summaries
 *
 * All operations are scoped to the authenticated user's company.
 */
class ExpenseController extends Controller
{
    use JsonResponseTrait;

    /**
     * Display a paginated list of expenses with optional filtering.
     *
     * Supports filtering by:
     * - Search term (title or category)
     * - Date range (start_date and end_date)
     *
     * @param ExpenseIndexRequest $request
     * @return JsonResponse
     */
    public function index(ExpenseIndexRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $companyId = Auth::user()->company_id;
        $cacheKey = "expenses:company:{$companyId}:page:{$validated['page']}:per_page:{$validated['per_page']}";

        if (isset($validated['search'])) {
            $cacheKey .= ":search:{$validated['search']}";
        }
        if (isset($validated['start_date'])) {
            $cacheKey .= ":start_date:{$validated['start_date']}";
        }
        if (isset($validated['end_date'])) {
            $cacheKey .= ":end_date:{$validated['end_date']}";
        }

        $expenses = Cache::remember($cacheKey, 3600, function () use ($validated, $companyId) {
            $query = Expense::where('company_id', $companyId);

            if (isset($validated['search'])) {
                $search = $validated['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
                });
            }

            if (isset($validated['start_date'])) {
                $query->whereDate('created_at', '>=', $validated['start_date']);
            }

            if (isset($validated['end_date'])) {
                $query->whereDate('created_at', '<=', $validated['end_date']);
            }

            return $query->paginate($validated['per_page'], ['*'], 'page', $validated['page']);
        });

        return $this->successResponse($expenses, 'Expenses retrieved successfully');
    }

    /**
     * Display the specified expense.
     *
     * @param Expense $expense
     * @return JsonResponse
     */
    public function show(Expense $expense): JsonResponse
    {
        $user = Auth::user();

        // Check if expense belongs to user's company
        if ($expense->company_id !== $user->company_id) {
            return $this->forbiddenResponse('You do not have permission to view this expense');
        }

        // Allow access if user is Admin, Manager, or owns the expense
        if ($user->role === 'Admin' || $user->role === 'Manager' || $expense->user_id === $user->id) {
            return $this->successResponse($expense, 'Expense retrieved successfully');
        }

        return $this->forbiddenResponse('You do not have permission to view this expense');
    }

    /**
     * Store a newly created expense in storage.
     *
     * @param ExpenseStoreRequest $request
     * @return JsonResponse
     */
    public function store(ExpenseStoreRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $expense = Expense::create([
            'user_id' => Auth::id(),
            'company_id' => Auth::user()->company_id,
            'title' => $validated['title'],
            'amount' => $validated['amount'],
            'category' => $validated['category'],
        ]);

        // Clear all expense-related caches for this company
        $companyId = Auth::user()->company_id;
        Cache::forget("expenses:company:{$companyId}:*");

        return $this->successResponse($expense, 'Expense created successfully', 201);
    }

    /**
     * Update the specified expense in storage.
     *
     * @param ExpenseUpdateRequest $request
     * @param Expense $expense
     * @return JsonResponse
     */
    public function update(ExpenseUpdateRequest $request, Expense $expense): JsonResponse
    {
        // Check if expense exists and belongs to user's company
        if (!$expense->exists) {
            return $this->errorResponse('Expense not found', 404);
        }

        $user = Auth::user();
        if ($expense->company_id !== $user->company_id) {
            return $this->forbiddenResponse('You do not have permission to update this expense');
        }

        // Check if user has permission to update
        if ($user->role !== 'Admin' && $user->role !== 'Manager') {
            return $this->forbiddenResponse('Only Managers and Admins can update expenses');
        }

        $validated = $request->validated();
        $expense->update($validated);

        // Clear all expense-related caches for this company
        $companyId = $user->company_id;
        Cache::forget("expenses:company:{$companyId}:*");

        return $this->successResponse($expense, 'Expense updated successfully');
    }

    /**
     * Remove the specified expense from storage.
     *
     * @param Expense $expense
     * @return JsonResponse
     */
    public function destroy(Expense $expense): JsonResponse
    {
        // Check if expense exists and belongs to user's company
        if (!$expense->exists) {
            return $this->errorResponse('Expense not found', 404);
        }

        $user = Auth::user();
        if ($expense->company_id !== $user->company_id) {
            return $this->forbiddenResponse('You do not have permission to delete this expense');
        }

        // Only Admins can delete expenses
        if ($user->role !== 'Admin') {
            return $this->forbiddenResponse('Only administrators can delete expenses');
        }

        $expense->delete();

        // Clear relevant caches
        $companyId = $user->company_id;
        Cache::forget("expenses:company:{$companyId}:*");

        return $this->successResponse(null, 'Expense deleted successfully');
    }

    /**
     * Generate a summary of expenses for the authenticated user's company.
     *
     * @return JsonResponse
     */
    public function summary(): JsonResponse
    {
        $companyId = Auth::user()->company_id;
        $expenses = Expense::where('company_id', $companyId)->get();

        $summary = [
            'total_expenses' => $expenses->sum('amount'),
            'average_expense' => $expenses->avg('amount'),
            'expenses_by_category' => $expenses->groupBy('category')->map(function ($items) {
                return [
                    'total' => $items->sum('amount'),
                    'count' => $items->count(),
                ];
            }),
        ];

        return $this->successResponse($summary, 'Expense summary retrieved successfully');
    }

    /**
     * Generate a weekly expense report.
     *
     * @param ExpenseReportService $service
     * @return JsonResponse
     */
    public function generateReport(ExpenseReportService $service): JsonResponse
    {
        $user = Auth::user();
        $company = $user->company;

        if (!$company) {
            return $this->errorResponse('Company not found', 404);
        }

        $report = $service->generateWeeklyReport($company);
        return $this->successResponse($report, 'Weekly expense report generated successfully');
    }
}
