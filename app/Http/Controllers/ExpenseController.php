<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Expense;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ExpenseController extends Controller
{
    // public function index(Request $request): JsonResponse
    // {
    //     $user = Auth::user();

    //     $search = $request->query('search'); // Search term for title or category
    //     $perPage = $request->query('per_page', 10); // Number of items per page, default to 10

    //     $expenses = Expense::where('company_id', $user->company_id)
    //         ->when($search, function ($query, $search) {
    //             $query->where('title', 'like', "%{$search}%")
    //                 ->orWhere('category', 'like', "%{$search}%");
    //         })->with('user')
    //         ->paginate($perPage);

    //     return response()->json([
    //         'message' => 'Successfully',
    //         'data' => $expenses,
    //     ], 200);
    // }

    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();

        $search = $request->query('search'); // title or category
        $perPage = $request->query('per_page', 10); // default to 10
        $page = $request->query('page', 1); // get current page number

        $cacheKey = "company_{$user->company_id}_expenses_" . md5("search={$search}&per_page={$perPage}&page={$page}");

        $expenses = Cache::remember($cacheKey, 3600, function () use ($user, $search, $perPage) {
            return Expense::where('company_id', $user->company_id)
                ->when($search, function ($query, $search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
                })
                ->with('user')
                ->paginate($perPage);
        });

        return response()->json([
            'message' => 'Successfully',
            'data' => $expenses,
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();

        $request->validate([
            'title' => ['required', 'string'],
            'amount' => ['required', 'numeric'],
            'category' => ['required', 'string'],
        ]);

        try {
            $expense = Expense::create([
                "user_id" => $user->id,
                "company_id" => $user->company_id,
                "title" => $request->title,
                "amount" => $request->amount,
                "category" => $request->category,
            ]);

            Cache::flush();

            return response()->json([
                'message' => 'Successfully',
                'data' => $expense,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating expense',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        $user = Auth::user();

        $request->validate([
            'title' => ['required', 'string'],
            'amount' => ['required', 'numeric'],
            'category' => ['required', 'string'],
        ]);

        try {

            $expense = Expense::where('company_id', $user->company_id)->findOrFail($id);

            if (!$expense) {
                return response()->json([
                    'message' => 'Expense not found or you do not have permission to update it',
                ], 404);
            }

            $oldValues = $expense->toArray();

            $expense->update([
                "title" => $request->title,
                "amount" => $request->amount,
                "category" => $request->category,
            ]);

            $newValues = $expense->fresh()->toArray();

            AuditLog::create([
                'user_id' => $user->id,
                'company_id' => $user->company_id,
                'action' => 'update',
                'changes' => [
                    'old' => $oldValues,
                    'new' => $newValues,
                ],
            ]);

            Cache::flush();

            return response()->json([
                'message' => 'Expense updated successfully',
                'data' => $expense,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating expense',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        $user = Auth::user();

        $request->validate([
            'title' => ['required', 'string'],
            'amount' => ['required', 'numeric'],
            'category' => ['required', 'string'],
        ]);

        try {

            $expense = Expense::where('company_id', $user->company_id)->findOrFail($id);

            if (!$expense) {
                return response()->json([
                    'message' => 'Expense not found or you do not have permission to delete it',
                ], 404);
            }

            $oldValues = $expense->toArray();

            $expense->delete();

            AuditLog::create([
                'user_id' => $user->id,
                'company_id' => $user->company_id,
                'action' => 'delete',
                'changes' => [
                    'old' => $oldValues,
                ],
            ]);

            Cache::flush();

            return response()->json([
                'message' => 'Expense deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting expense',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
