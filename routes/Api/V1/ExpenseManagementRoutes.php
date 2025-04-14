<?php

use App\Http\Controllers\V1\ExpenseManagement\CreateExpenseController;
use App\Http\Controllers\V1\ExpenseManagement\DeleteExpenseController;
use App\Http\Controllers\V1\ExpenseManagement\FetchExpensesController;
use App\Http\Controllers\V1\ExpenseManagement\GetExpenseController;
use App\Http\Controllers\V1\ExpenseManagement\UpdateExpenseController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['auth']
], function () {
    Route::delete('/expenses/{expenseId}', DeleteExpenseController::class);

    Route::put('/expenses/{expenseId}', UpdateExpenseController::class);

    Route::get('/expenses/{expenseId}', GetExpenseController::class);

    Route::post('/expenses', CreateExpenseController::class);

    Route::get('/expenses', FetchExpensesController::class);
});
