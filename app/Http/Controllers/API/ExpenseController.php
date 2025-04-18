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

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return ExpenseResource::collection(
            Expense::search($request->query('search'))->paginate(10)
        );
    }


    /**
     * Store a newly created resource in storage.
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
     * Update the specified resource in storage.
     */
    public function update(UpdateExpenseRequest $request, Expense $expense)
    {
        Gate::authorize('update', $expense);

        $expense->update($request->validated());

        return ExpenseResource::make($expense);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        Gate::authorize('delete', $expense);
        $expense->delete();
        return $this->customJsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
