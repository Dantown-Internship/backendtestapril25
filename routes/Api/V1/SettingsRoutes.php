<?php

use App\Http\Controllers\V1\Settings\ExpenseCategory\CreateExpenseCategoryController;
use App\Http\Controllers\V1\Settings\ExpenseCategory\DeleteExpenseCategoryController;
use App\Http\Controllers\V1\Settings\ExpenseCategory\FetchExpenseCategoriesController;
use App\Http\Controllers\V1\Settings\ExpenseCategory\UpdateExpenseCategoryController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['auth:sanctum']
], function () {
    Route::delete('/expense-categories/{expenseCategoryId}', DeleteExpenseCategoryController::class);

    Route::put('/expense-categories/{expenseCategoryId}', UpdateExpenseCategoryController::class);

    Route::post('/expense-categories', CreateExpenseCategoryController::class);

    Route::get('/expense-categories', FetchExpenseCategoriesController::class);
});
