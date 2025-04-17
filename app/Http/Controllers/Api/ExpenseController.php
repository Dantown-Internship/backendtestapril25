<?php

namespace App\Http\Controllers\Api;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\ListExpensesRequest;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Http\Resources\ExpenseResource;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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
            data: new ExpenseResource($expense)
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $uuid)
    {
        $role = $request->user()->role;
        $query = Expense::where('uuid', $uuid);
        $query = match ($role) {
            Role::Employee => $query->where('user_id', $request->user()->id),
            Role::Manager => $query->with('user'),
            Role::Admin => $query->with('user'),
        };
        $expense = $query->firstOrFail();

        return $this->successResponse(
            message: 'Expense retrieved successfully.',
            data: new ExpenseResource($expense)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateExpenseRequest $request, string $uuid)
    {
        $expense = $request->expense;
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $uuid)
    {
        abort_if(
            $request->user()->role !== Role::Admin,
            403,
            'You are not authorized to delete this expense.'
        );
        $expense = Expense::where('uuid', $uuid)->firstOrFail();
        $expense->delete();

        return $this->successResponse(
            message: 'Expense deleted successfully.'
        );
    }
}
