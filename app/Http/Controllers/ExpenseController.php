<?php

namespace App\Http\Controllers;

use App\Http\Filter\ExpenseFilter;
use App\Http\Library\ApiHelpers;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ExpenseController extends Controller
{
    use ApiHelpers;
    /**
     * Display a listing of the resource.
     */
    public function index(ExpenseFilter $filter)
    {
        //
        $expenses = Cache::remember('expenses', 60, function () use ($filter) {
            return Expense::filter($filter)->with(['company'])->latest()->paginate(10);
        });
        // $expenses = Expense::filter($filter)->with(['company'])->latest()->paginate(10);
        // Return a response
        return $this->onSuccess(message: 'Expenses retrieved successfully', data: $expenses);

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
    public function store(Request $request)
    {
        //
        // Validate the request data
        $validatedData = $request->validate([
            'amount' => 'required|numeric',
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:255',
        ]);
        // Create a new expense
        $expense = new Expense();
        $expense->amount = $validatedData['amount'];
        $expense->title = $validatedData['title'];
        $expense->category = $validatedData['category'];
        $expense->user_id = auth()->id();
        $expense->save();
        // Return a response
        return $this->onSuccess(message: 'Expense created successfully', data: $expense);
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
    public function update(Request $request, Expense $expense)
    {
        //
        // Validate the request data   
        if (!($this->isAdmin(auth()->user()) || $this->isManager(auth()->user()))) {
            return $this->onError(code: 403, message: 'You are not authorized to update this expense');
        }
        $validatedData = $request->validate([
            'amount' => 'sometimes|numeric',
            'title' => 'sometimes|string|max:255',
            'category' => 'sometimes|string|max:255',
        ]);
        // Update the expense
        $expense->update($validatedData);
        // Return a response
        return $this->onSuccess(message: 'Expense updated successfully', data: $expense);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        //
        if (!($this->isAdmin(auth()->user()))) {
            return $this->onError(code: 403, message: 'You are not authorized to delete this expense');
        }
        $expense->delete();
        // Return a response  
        return $this->onSuccess(message: 'Expense deleted successfully', data: null);
    }
}
