<?php

use App\Http\Controllers\V1\ExpenseManagement\CreateExpenseController;
use App\Http\Controllers\V1\ExpenseManagement\DeleteExpenseController;
use App\Http\Controllers\V1\ExpenseManagement\FetchExpensesController;
use App\Http\Controllers\V1\ExpenseManagement\GetExpenseController;
use App\Http\Controllers\V1\ExpenseManagement\UpdateExpenseController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['auth:sanctum']
], function () {
    Route::delete('/expenses/{expenseId}', DeleteExpenseController::class)->middleware('adminOnlyAuthorization');

    Route::put('/expenses/{expenseId}', UpdateExpenseController::class)->middleware('adminAndManagerAuthorization');

    Route::get('/expenses/{expenseId}', GetExpenseController::class);

    Route::post('/expenses', CreateExpenseController::class);

    Route::get('/expenses', FetchExpensesController::class);
});
