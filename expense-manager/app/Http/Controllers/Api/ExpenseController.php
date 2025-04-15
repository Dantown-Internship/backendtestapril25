<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{

    use ApiResponse;

    /**
     * All Expenses List
     */
    public function index()
    {
        //
    }

    /**
     * Create Expense
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Get a Expenses
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update Record (Managers & Admins only)
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     *  Delete Record (Admins only)
     */
    public function destroy(string $id)
    {
        //
    }
}
