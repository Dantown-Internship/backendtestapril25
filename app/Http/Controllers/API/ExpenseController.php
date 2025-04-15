<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Models\AuditLog;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;


class ExpenseController extends Controller
{
    /**
     * Display a listing of the expenses for the user's company.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $companyId = $user->company_id;

        // Get search and pagination parameters
        $search = $request->query('search');
        $page = $request->query('page', 1);

        // Unique cache key based on company, search and page
        $cacheKey = "expenses_{$companyId}_search_{$search}_page_{$page}";

        // Cache the result for 5 minutes (300 seconds)
        
        // Cache the result for 5 minutes (300 seconds)
        $expenses = Cache::remember($cacheKey, 300, function () use ($companyId, $search) {
            return Expense::with(['category', 'user'])
                ->where('company_id', $companyId)
                ->when($search, function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('title', 'like', "%{$search}%")
                          ->orWhereHas('category', function ($q2) use ($search) {
                              $q2->where('name', 'like', "%{$search}%");
                          });
                    });
                })
                ->latest()
                ->paginate(10);
        });

        return response()->json([
            'message' => 'Expenses fetched successfully',
            'data' => $expenses,
        ], 200);
    }

    /**
     * Store a newly created expense in storage.
     * Clears related cache to reflect new data.
     * @param  \App\Http\Requests\StoreExpenseRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreExpenseRequest $request)
    {
        $user = Auth::user();

        // Clear cache to reflect new expense
        Cache::flush();

        // Create a new expense for the logged-in user's company
        $expense = Expense::create([
            'user_id' => $user->id,
            'company_id' => $user->company_id,
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'title' => $request->title,
        ]);

        return response()->json([
            'message' => 'Expense created successfully',
            'data' => $expense,
        ], 201);
    }

    /**
     * Update the specified expense.
     * Clears cache to reflect updated data.
     * Logs the changes to the audit log.
     * @param  \App\Http\Requests\UpdateExpenseRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateExpenseRequest $request, $id)
    {
        $user = Auth::user();
        
        // Clear cache to reflect updated expense
        Cache::flush();


        // Find the expense within the same company
        $expense = Expense::where('id', $id)
            ->where('company_id', $user->company_id)
            ->firstOrFail();

        // Store old values for audit log
        $oldValues = $expense->only(['title', 'amount', 'category_id']);

        // Update only allowed fields
        $expense->update($request->only([
            'category_id',
            'amount',
            'title',
        ]));

        // Store new values for audit log
        $newValues = $expense->only(['title', 'amount', 'category_id']);

        // Create an audit log entry
        AuditLog::create([
            'user_id' => $user->id,
            'company_id' => $user->company_id,
            'action' => 'update',
            'changes' => json_encode([
                'old' => $oldValues,
                'new' => $newValues,
            ]),
        ]);

        return response()->json([
            'message' => 'Expense updated successfully',
            'data' => $expense,
        ], 200);
    }

    /**
     * Remove the specified expense.
     * Clears cache to reflect deleted data.
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
    */
    public function destroy($id)
    {

        $user = Auth::user();

        // Clear cache to reflect deleted expense
        Cache::flush();
        
        // Find the expense within the same company
        $expense = Expense::where('id', $id)
            ->where('company_id', $user->company_id)
            ->firstOrFail();

        // Capture data before deletion for audit
        $oldValues = $expense->only(['title', 'amount', 'category_id']);

        $expense->delete();

        // Log deletion
        AuditLog::create([
            'user_id' => $user->id,
            'company_id' => $user->company_id,
            'action' => 'delete',
            'changes' => json_encode([
                'old' => $oldValues,
                'new' => null,
            ]),
        ]);
        
        return response()->json([
            'message' => 'Expense deleted successfully',
        ], 200);
    }
}
