<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Management\ExpenseController as CompanyExpenses;



Route::middleware('auth:sanctum')->group(function () {
    Route::post('/create', [CompanyExpenses::class, 'create']);
    Route::get('/', [CompanyExpenses::class, 'expenses']);
    Route::put('/{expenseId?}', [CompanyExpenses::class, 'update']);
    Route::delete('/delete/{expenseId}', [CompanyExpenses::class, 'delete']);

    
});
