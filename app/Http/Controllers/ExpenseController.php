<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Services\ExpenseService;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    protected $expenseService;

    public function __construct(ExpenseService $expenseService)
    {
        $this->expenseService = $expenseService;
    }

    public function index(Request $request)
    {
        return $this->expenseService->index($request);
    }

    public function store(StoreExpenseRequest $request)
    {
        return $this->expenseService->store($request->validated());
    }

    public function update(UpdateExpenseRequest $request, $id)
    {
        return $this->expenseService->update($request->validated(), $id);
    }

    public function destroy($id)
    {
        return $this->expenseService->destroy($id);
    }
}
