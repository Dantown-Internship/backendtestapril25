<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseRequest;
use App\Models\AuditLog;
use App\Models\Company;
use App\Models\Expense;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class ExpensesManangementController extends Controller
{
    public function creatExpenses(ExpenseRequest $request): \Illuminate\Http\JsonResponse
    {

        try {
            $validatedData = $request->validated();

            $user = Auth::user();

            // Create the expense tied to the authenticated user's company and user_id

            $store = Expense::create([
                'company_id' => $user->company_id,
                'user_id'    => $user->id,
                'title' => $validatedData['title'],
                'amount' => $validatedData['amount'],
                'category' => $validatedData['category']
            ]);

            // Check if the expense was saved
            if (!$store) {
                return response()->json([
                    'message' => 'Unable to save expense!'
                ], 400);
            }

            // Return success message with created expense details
            return response()->json([
                'message' => 'Expense saved successfully!',
                'expense' => $store
            ], 201);
        } catch (Exception $e) {
            // Return error message in case of an exception
            return response()->json([
                'error_message' => $e->getMessage()
            ], 500); // Internal server error for unexpected issues
        }
    }

    public function updateExpenses(ExpenseRequest $request, $id): \Illuminate\Http\JsonResponse
    {
        $validatedData = $request->validated();

        $expense = Expense::query()->where('id', $id)->first();

        if(!$expense)
        {
            return response()->json([
                'message' => 'Expenses not found!'
            ], 404);
        }

        if ($expense->company_id !== Auth::user()->company_id) {
            return response()->json(['message' => 'Unauthorized access.'], 403);
        }

        $oldData = json_decode(json_encode($expense->toArray()), true); // deep copy

        $expense->update($validatedData);

        $newData = $expense->fresh()->toArray();
        // Create audit log
        AuditLog::create([
            'user_id' => Auth::id(),
            'company_id' => $expense->company_id,
            'action' => 'update_expense',
            'changes' => json_encode([
                'old' => $oldData,
                'new' => $newData
            ])
        ]);

        return response()->json([
            'message' => 'Expenses updated!',
            'update expenses' => $expense
        ], 200);
    }

    public function deleteExpense($id): \Illuminate\Http\JsonResponse
    {
        $expense = Expense::query()->where('id', $id)->first();

        if(!$expense)
        {
            return response()->json([
                'message' => 'Expenses not found!'
            ], 404);
        }

        if ($expense->company_id !== Auth::user()->company_id) {
            return response()->json(['message' => 'Unauthorized access.'], 403);
        }

        $oldData = $expense->toArray();

        $expense->delete();

        // Create audit log
        AuditLog::create([
            'user_id' => Auth::id(),
            'company_id' => $oldData['company_id'],
            'action' => 'delete_expense',
            'changes' => json_encode([
                'old' => $oldData
            ])
        ]);

        return response()->json([
            'message' => 'Expenses deleted!'
        ], 200);
    }

    public function getExpenses(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();

        // Get optional filters from the request
        $searchTitle = $request->input('title');
        $searchCategory = $request->input('category');

        $perPage = $request->input('per_page', 10); // Default pagination is 10

        // Generate a unique cache key
        $cacheKey = 'expenses_' . $user->company_id . '_' . md5($searchTitle . '_' . $searchCategory . '_' . $perPage);

            // Check cache first
        if (Cache::has($cacheKey)) {
            $expenses = Cache::get($cacheKey);
        } else {
            $expenses = Expense::with(['company', 'user'])
                ->where('company_id', $user->company_id) // Restrict to user's company
                ->when($searchTitle, function ($query, $searchTitle) {
                    $query->where('title', 'ILIKE', "%$searchTitle%");
                })
                ->when($searchCategory, function ($query, $searchCategory) {
                    $query->where('category', 'ILIKE', "%$searchCategory%");
                })
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            Cache::put($cacheKey, $expenses, now()->addMinutes(10));

        }

        return response()->json([
            'message' => 'Expenses retrieved successfully',
            'data' => $expenses
        ], 200);
    }

    public function createCompany(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'email' => 'required|string|unique:companies'
            ]);
        }catch (ValidationException $e)
        {
            return response()->json(['message' => $e->getMessage()], 401);
        }

        $company = Company::create([
            'name' => $request->input('name'),
            'email' => $request->input('email')
        ]);

        return response()->json([
            'message' => 'Company created successfully!',
            'company' => $company
        ], 201);
    }
}
