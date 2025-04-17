<?php

use App\Http\Controllers\ExpenseController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/expenses', [ExpenseController::class, "index"]);
    Route::post('/expenses', [ExpenseController::class, "store"]);
    Route::put('/expenses/{expense}', [ExpenseController::class, "update"]);
    Route::delete('/expenses/{expense}', [ExpenseController::class, "destroy"]);
});