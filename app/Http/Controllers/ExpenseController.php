<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Http\Requests\CreateExpense;
use App\Models\Expense;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{

    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $page = 10)
    {
        $user = auth()->user();
        $companyId = $user->company_id;

        $query = Expense::where('company_id', $companyId)->with('user');

        // If the user is a manager or admin, show all expenses for the company
        if ($user->hasRole(RoleEnum::MANAGER) || $user->hasRole(RoleEnum::ADMIN)) {
            $query->where('company_id', $companyId);
        } else {
            $query->where('user_id', $user->id);
        }

        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        $perPage = (int) $request->get('per_page', $page);

        $expenses = $query->latest()->paginate($perPage);

        return $this->success($expenses, 'Fetched expenses successfully.');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateExpense $request)
    {

        $user = auth()->user();
        $validated = $request->validated();

        $expense = $user->expenses()->create([
            'amount' => $validated['amount'],
            'title' => $validated['title'],
            'category' => $validated['category'],
            'company_id' => $validated['company_id'],
        ]);

        return $this->success($expense, 'Expense created successfully.', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Expense $expense)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Expense $expense)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CreateExpense $request, Expense $expense)
    {
        $user = auth()->user();

        $validated = $request->validated();


        if (!$expense) {
            return $this->notFound('expense not found');
        }

        if ((!$user->hasRole(RoleEnum::ADMIN) || !$user->hasRole(RoleEnum::MANAGER)) && $expense->company_id != $user->company_id) {
            return $this->forbidden('You do not have permission to update this expense');
        }
        $expense->update($validated);

        return $this->success($expense, 'Expense updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        $user = auth()->user();

        try {
            if (!$expense) {
                return $this->notFound('expense not found');
            }
            if (!$user->hasRole(RoleEnum::ADMIN)) {
                return $this->forbidden('You do not have permission to delete this expense.');
            }
            $expense->delete();
            return $this->success([], "expense deleted successfully");
        } catch (\Throwable $th) {
            $this->error($th->getMessage());
        }

    }
}
