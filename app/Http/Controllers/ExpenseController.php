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
        $user = Auth::user();

        // Set default values for pagination
        $page = $validated['page'] ?? 1;
        $perPage = $validated['per_page'] ?? 15;

        $cacheKey = "expenses:company:{$user->company_id}:page:{$page}:per_page:{$perPage}";

        if (isset($validated['search'])) {
            $cacheKey .= ":search:{$validated['search']}";
        }
        if (isset($validated['start_date'])) {
            $cacheKey .= ":start_date:{$validated['start_date']}";
        }
        if (isset($validated['end_date'])) {
            $cacheKey .= ":end_date:{$validated['end_date']}";
        }

        $expenses = Cache::remember($cacheKey, 3600, function () use ($validated, $user, $page, $perPage) {
            $query = Expense::where('company_id', $user->company_id);

            // If user is not admin or manager, only show their own expenses
            if ($user->role !== 'Admin' && $user->role !== 'Manager') {
                $query->where('user_id', $user->id);
            }

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

            return $query->paginate($perPage, ['*'], 'page', $page);
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

        // Check if user has permission to view this expense
        if ($user->role !== 'Admin' && $user->role !== 'Manager' && $expense->user_id !== $user->id) {
            return $this->unauthorizedResponse('You do not have permission to view this expense');
        }

        return $this->successResponse($expense, 'Expense retrieved successfully');
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
        $user = Auth::user();

        $expense = Expense::create([
            'title' => $validated['title'],
            'amount' => $validated['amount'],
            'category' => $validated['category'],
            'company_id' => $user->company_id,
            'user_id' => $user->id,
            'date' => $validated['date'] ?? now(),
        ]);

        // Clear cache for this company's expenses
        Cache::forget("expenses:company:{$user->company_id}");

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
        $user = Auth::user();

        // Check if user has permission to update this expense
        if ($user->role !== 'Admin' && $user->role !== 'Manager' && $expense->user_id !== $user->id) {
            return $this->unauthorizedResponse('You do not have permission to update this expense');
        }

        $validated = $request->validated();
        $expense->update($validated);

        // Clear cache for this company's expenses
        Cache::forget("expenses:company:{$user->company_id}");

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
        $user = Auth::user();

        // Only admins can delete expenses
        if ($user->role !== 'Admin') {
            return $this->forbiddenResponse('Only administrators can delete expenses');
        }

        $expense->delete();

        // Clear cache for this company's expenses
        Cache::forget("expenses:company:{$user->company_id}");

        return $this->successMessage('Expense deleted successfully');
    }

    /**
     * Generate a summary of expenses for the authenticated user's company.
     *
     * @return JsonResponse
     */
    public function summary(): JsonResponse
    {
        $user = Auth::user();
        $query = Expense::where('company_id', $user->company_id);

        // If user is not admin or manager, only show their own expenses
        if ($user->role !== 'Admin' && $user->role !== 'Manager') {
            $query->where('user_id', $user->id);
        }

        $expenses = $query->get();

        $summary = [
            'total_expenses' => $expenses->sum('amount'),
            'average_expense' => $expenses->avg('amount'),
            'expenses_by_category' => $expenses->groupBy('category')->map(function ($items) {
                return [
                    'total' => $items->sum('amount'),
                    'count' => $items->count(),
                ];
            })->toArray(),
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
