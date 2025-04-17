<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExpenseController;


Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    //User Api
    //Admin on registercd 
    Route::post('/register', [AuthController::class, 'register'])->middleware('role:Admin');
    Route::post('/logout', [AuthController::class, 'logout']);


    //Expenses Api
    Route::get('/expenses', [ExpenseController::class, 'index']);
    Route::post('/expenses', [ExpenseController::class, 'create']);

    //Update expense - Manager or Admin
    Route::put('/expenses/{id}', [ExpenseController::class, 'update'])->middleware('role:Manager|Admin');

    // Delete expense - only Admins
    Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy'])->middleware('role:Admin');
});

Route::middleware(['auth:sanctum', 'role:Admin'])->get('/check', function () {
    return response()->json(['status' => 'ok', 'user' => auth()->user()]);
});