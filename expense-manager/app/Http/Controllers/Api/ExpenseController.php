<?php

namespace App\Http\Controllers\Api;

use App\Actions\Expenses\DeleteExpensesAction;
use App\Actions\Expenses\GetExpensesAction;
use App\Actions\Expenses\ListExpensesAction;
use App\Actions\Expenses\StoreExpensesAction;
use App\Actions\Expenses\UpdateExpensesAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Http\Resources\ExpenseResource;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    use ApiResponse;
    /**
     * All Expenses List
     */
    public function index(Request $request, ListExpensesAction $action)
    {
        $request->validate([
            'search' =>'sometimes|string',
            'category' => 'sometimes|string'
        ]);
        $filters = $request->only(['search', 'category']);
        $perPage = $request->input('per_page', 15);

        $paginated = $action->handle($filters, $perPage);


        $items = ExpenseResource::collection($paginated->items());

        return $this->paginatedResponse('Expenses fetched successfully', $items, $paginated);

    }

    /**
     * Create Expense
     */
    public function store(StoreExpenseRequest $request, StoreExpensesAction $action)
    {
        $expense = $action->handle($request->validated());
        return (new ExpenseResource($expense))
            ->additional([
                'success' => true,
                'message' => 'Expense created successfully'
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Get a Expenses
     */
    public function show(string $id, GetExpensesAction $action)
    {
        $expense = $action->handle($id);

        return (ExpenseResource::make(
            $expense
        ))->additional([
            'success' => true,
            'message' => 'Expense fetched successfully'
        ]);
    }

    /**
     * Update Record (Managers & Admins only)
     */
    public function update(UpdateExpenseRequest  $request, string $id)
    {
        $action = new UpdateExpensesAction();
        $expense = $action->handle($id, $request->validated());

        return (new ExpenseResource($expense))->additional([
            'success' => true,
            'message' => 'Expense updated successfully'
        ]);
    }

    /**
     *  Delete Record (Admins only)
     */
    public function destroy(string $id, DeleteExpensesAction $action)
    {
        $action->handle($id);

        return response()->json(null, 204);
    }
}
