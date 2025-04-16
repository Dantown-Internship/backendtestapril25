<?php

namespace App\Http\Controllers;

use App\Http\Library\ApiHelpers;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    use ApiHelpers;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        $expense->user_id = auth()->id(); // Assuming you have a user_id field in your expenses table
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
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        //
    }
}
