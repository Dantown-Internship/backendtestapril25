<?php

namespace App\Http\Controllers;

use App\Http\Requests\Expenses\StoreRequest;
use App\Http\Requests\Expenses\UpdateRequest;
use App\Http\Resources\ExpenseResource;
use App\Models\Expense;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

class ExpenseController extends Controller
{
    private $authGuard;

    public function __construct()
    {
        $this->authGuard = auth('sanctum');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $term = request('search', '');

        $key = 'expenses_' . $this->authGuard->user()->company_id . '_' . $term;

        return Cache::remember($key, 60, function () use ($term) {
            $expenses = Expense::with('user')->companyExpenses($this->authGuard->user()->company_id)
                ->search($term)
                ->paginate();
            return ExpenseResource::collection($expenses)
                ->additional([
                    'meta' => [
                        'message' => 'Expenses retrieved successfully',
                        'status' => 200,
                    ],
                ]);
        });
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $request->validated();

        $expense = Expense::create([
            'title' => $request->title,
            'amount' => $request->amount,
            'category' => $request->category,
            'user_id' => $this->authGuard->user()->id,
            'company_id' => $this->authGuard->user()->company_id,
        ]);

        return (new ExpenseResource($expense))
            ->additional([
                'meta' => [
                    'message' => 'Expense created successfully',
                    'status' => 201,
                ],
            ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Expense $expense)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Expense $expense)
    {
        $request->validated();

        $expense->update($request->only(['title', 'amount', 'category']));

        return (new ExpenseResource($expense))
            ->additional([
                'meta' => [
                    'message' => 'Expense updated successfully',
                    'status' => 200,
                ],
            ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        Gate::authorize('delete', $expense);
        $expense->delete();
        return response()->json([
            'message' => 'Expense deleted successfully',
            'status' => 200,
        ]);
    }
}
