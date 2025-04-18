<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Http\Resources\ExpenseResource;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

/**
 * @group Expenses
 * 
 * Expense Related Apis
 */
class ExpenseController extends Controller
{
    /**
     * List all products
     * 
     * This endpoint returns a paginated list of all expenses
     * 
     * @apiResourceCollection App\Http\Resources\ExpenseResource
     * @apiResourceModel App\Models\Expense paginate=10
     */
    public function index(Request $request)
    {
        return ExpenseResource::collection(
            Expense::search($request->query('search'))->paginate(10)
        );
    }


    /**
     * Create New Expense
     * 
     * This endpoint creates a new expense
     *
     * @apiResource App\Http\Resources\ExpenseResource
     * @apiResourceModel App\Models\Expense 
     */
    public function store(StoreExpenseRequest $request)
    {
        $user = $request->user();

        $expense = Expense::create([
            ...$request->validated(),
            'user_id' => $user->id,
            'company_id' => $user->company_id,
        ]);

        return ExpenseResource::make($expense);
    }


    /**
     * Updates Single Expense
     * 
     * This endpoint updates a single expense
     *
     * @apiResource App\Http\Resources\ExpenseResource
     * @apiResourceModel App\Models\Expense 
     * @response 404 { "data": {"message": "App\Models\Expense not found"}}
     */
    public function update(UpdateExpenseRequest $request, Expense $expense)
    {
        Gate::authorize('update', $expense);

        $expense->update($request->validated());

        return ExpenseResource::make($expense);
    }

    /**
     * Updates Single Expense
     * 
     * This endpoint updates a single expense.
     *
     * @response 204 {"data": null}
     * @response 404 {
     *   "data": {
     *     "message": "App\\Models\\Expense not found"
     *   }
     * }
     */
    public function destroy(Expense $expense)
    {
        Gate::authorize('delete', $expense);
        $expense->delete();
        return $this->customJsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
