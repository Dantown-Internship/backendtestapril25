<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Expense\StoreExpenseRequest;
use App\Http\Requests\Expense\UpdateExpenseRequest;
use App\Http\Resources\ExpenseResource;
use App\Models\Expense;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ExpenseController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('can:index,'.Expense::class, only: ['index']),
            new Middleware('can:store,'.Expense::class, only: ['store']),
            new Middleware('can:update,expense', only: ['update']),
            new Middleware('can:destroy,expense', only: ['destroy']),
        ];
    }

    public function index(): AnonymousResourceCollection
    {
        $expenses = Expense::with('user')
            ->where('company_id', auth()->user()->company_id)
            ->when(request('search'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate();

        return ExpenseResource::collection($expenses)
            ->additional([
                'status' => 'success',
                'message' => 'Expenses fetched successfully',
            ]);
    }

    public function store(StoreExpenseRequest $request): JsonResponse
    {
        $expense = Expense::create([
            ...$request->validated(),
            'user_id' => auth()->id(),
            'company_id' => auth()->user()->company_id,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Expense created successfully',
            'data' => new ExpenseResource($expense),
        ], 201);
    }

    public function update(UpdateExpenseRequest $request, Expense $expense): JsonResponse
    {
        $expense->update($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Expense updated successfully',
            'data' => new ExpenseResource($expense),
        ]);
    }

    public function destroy(Expense $expense): JsonResponse
    {
        $expense->delete();

        return response()->json([], 204);
    }
}