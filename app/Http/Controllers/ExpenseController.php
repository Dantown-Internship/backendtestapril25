<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');

    }

    public function index(Request $request)
    {
        $user = $request->user();

        $query = Expense::where('company_id', $user->company_id)
            ->with('user:id,name') // Eager load user who created the expense
            ->latest(); // Order by created_at DESC

        // Search by title or category
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Paginate (default 15 per page if not passed)
        return response()->json(
            $query->paginate($request->get('per_page', 15))
        );
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }


        $expense = $request->user()->expenses()->create([
            'title' => $request->title,
            'amount' => $request->amount,
            'category' => $request->category,
            'description' => $request->description,
            'company_id' => $request->user()->company_id,
        ]);


        // Log the creation
        AuditLog::create([
            'user_id' => $request->user()->id,
            'company_id' => $request->user()->company_id,
            'action' => 'create',
            'model_type' => Expense::class,
            'model_id' => $expense->id,
            'changes' => $expense->toArray(),
            'ip_address' => $request->ip(),
        ]);

        return response()->json($expense, 201);
    }

    public function update(Request $request, Expense $expense)
    {
        $user = $request->user();

        // Additional role verification
        if (!$user->isAdmin() && !$user->isManager()) {
            return response()->json(['message' => 'Insufficient privileges'], 403);
        }

        // Verify expense belongs to user's company
        if ($expense->company_id !== $user->company_id) {
            return response()->json(['message' => 'Expense not found'], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'amount' => 'sometimes|numeric|min:0',
            'category' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            $oldData = $expense->toArray();
            $expense->update($validated);

            AuditLog::create([
                'user_id' => $user->id,
                'company_id' => $user->company_id,
                'action' => 'update',
                'model_type' => Expense::class,
                'model_id' => $expense->id,
                'changes' => [
                    'old' => $oldData,
                    'new' => $expense->fresh()->toArray()
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'success' => true,
                'data' => $expense->fresh(),
                'message' => 'Expense updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update expense',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function destroy(Request $request, Expense $expense)
    {
        $user = $request->user();

        // 1. Verify user is Admin
        if (!$user->isAdmin()) {
            return response()->json(['message' => 'Admin privileges required'], 403);
        }

        // 2. Verify user and expense belong to same company (multiple checks)
        if ($user->company_id !== $expense->company_id) {
            return response()->json(['message' => 'Expense not found in your company records'], 404);
        }

        // 3. Verify the user actually exists in the expense's company
        $companyUsers = User::where('company_id', $expense->company_id)
                           ->pluck('id')->toArray();

        if (!in_array($user->id, $companyUsers)) {
            return response()->json(['message' => 'You are not authorized for this company'], 403);
        }

        try {
            // 4. Final verification before deletion
            if ($expense->company_id !== $user->company_id) {
                throw new \Exception('Company verification failed at deletion point');
            }

            $oldData = $expense->toArray();
            $expense->delete();

            AuditLog::create([
                'user_id' => $user->id,
                'company_id' => $user->company_id,
                'action' => 'delete',
                'model_type' => Expense::class,
                'model_id' => $expense->id,
                'changes' => $oldData,
                'ip_address' => $request->ip(),
                'metadata' => [
                    'verification_steps' => [
                        'admin_check' => true,
                        'company_match' => true,
                        'user_in_company' => true
                    ]
                ]
            ]);

            return response()->json(['success' => 'Expense with id '.$expense->id.' Deleted successfully'], 204);

        } catch (\Exception $e) {
            // Log detailed error for investigation
            Log::error('Expense deletion failed', [
                'user_id' => $user->id,
                'expense_id' => $expense->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Deletion failed - security verification error',
                'error' => 'An internal error occurred'
            ], 500);
        }
    }
}