<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Libs\Actions\Expenses\UpdateExpenseAction;
use App\Libs\Actions\Expenses\GetUsersAction;
use App\Libs\Actions\Expenses\GetExpensesAction;
use App\Libs\Actions\Expenses\DeleteExpenseAction;
use App\Libs\Actions\Expenses\CreateExpenseAction;
use App\Http\Controllers\Controller;

final class ExpenseController extends Controller
{

    public function __construct(
        protected GetExpensesAction $getExpensesAction,
        protected CreateExpenseAction $createExpenseAction,
        protected UpdateExpenseAction $updateExpenseAction,
        protected DeleteExpenseAction $deleteExpenseAction,        
    ){}

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request$request)
    {
        return $this->getExpensesAction->handle($request);
    }

    /**
     * Create Expense Record
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:255',
        ]);
        return $this->createExpenseAction->handle($request);
    }

    /**
     * Update Expense Record
     * @param \Illuminate\Http\Request $request
     * @param mixed $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:255',
        ]);

        return $this->updateExpenseAction->handle($request, $id);
    }

    /**
     * Delete Expense Record
     * @param mixed $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        return $this->deleteExpenseAction->handle(request(), $id);
    }
}
