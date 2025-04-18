<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\ListExpensesRequest;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Http\Resources\ExpenseResource;
use App\Models\Expense;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(ListExpensesRequest $request)
    {
        $perPage = $request->validated('per_page', 10);
        $search = $request->validated('search');
        $role = $request->user()->role;
        $query = Expense::query()
            ->when(! blank($search), function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
                });
            });
        $expensesQuery = match ($role) {
            Role::Employee => $query->where('user_id', $request->user()->id),
            Role::Manager => $query->with('user'),
            Role::Admin => $query->with('user'),
        };
        $expenses = $expensesQuery->latest()->paginate($perPage)->withQueryString();

        return $this->paginatedResponse(
            message: 'Expenses retrieved successfully.',
            data: ExpenseResource::collection($expenses)
        );
    }

    public function store(StoreExpenseRequest $request)
    {
        $expense = Expense::create([
            'title' => $request->validated('title'),
            'amount' => $request->validated('amount'),
            'category' => $request->validated('category'),
            'user_id' => $request->user()->id,
            'company_id' => $request->user()->company_id,
        ]);

        return $this->successResponse(
            message: 'Expense created successfully.',
            data: new ExpenseResource($expense),
            statusCode: 201
        );
    }

    public function show(Request $request, Expense $expense)
    {
        $this->authorize('view', $expense);
        if(in_array($request->user()->role, [Role::Manager, Role::Admin])) {
            $expense->load('user');
        }

        return $this->successResponse(
            message: 'Expense retrieved successfully.',
            data: new ExpenseResource($expense)
        );
    }

    public function update(UpdateExpenseRequest $request, Expense $expense)
    {
        $expense->update([
            'title' => $request->validated('title'),
            'amount' => $request->validated('amount'),
            'category' => $request->validated('category'),
        ]);

        return $this->successResponse(
            message: 'Expense updated successfully.',
            data: new ExpenseResource($expense)
        );
    }

    public function destroy(Request $request, Expense $expense)
    {
        $this->authorize('delete', $expense);
        $expense->delete();

        return $this->successResponse(
            message: 'Expense deleted successfully.'
        );
    }
}
