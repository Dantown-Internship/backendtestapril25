<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Libs\Actions\Expenses\UpdateExpenseAction;
use App\Libs\Actions\Expenses\GetUsersAction;
use App\Libs\Actions\Expenses\GetExpensesAction;
use App\Libs\Actions\Expenses\DeleteExpenseAction;
use App\Libs\Actions\Expenses\CreateExpenseAction;
use App\Http\Controllers\Controller;

class ExpenseController extends Controller
{

    public function __construct(
        protected GetExpensesAction $getExpensesAction,
        protected GetUsersAction $getUsersAction,
        protected CreateExpenseAction $createExpenseAction,
        protected UpdateExpenseAction $updateExpenseAction,
        protected DeleteExpenseAction $deleteExpenseAction,        
    ){}
    public function index(Request$request)
    {
        return $this->getExpensesAction->handle($request);
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:255',
        ]);
        return $this->createExpenseAction->handle($request);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:255',
        ]);
        
        return $this->updateExpenseAction->handle($request, $id);
    }

    public function destroy($id)
    {
        return $this->deleteExpenseAction->handle(request(), $id);
    }
}
