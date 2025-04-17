<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\UserController;


Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    //AUTH API
    //Admin on registercd 
    Route::post('/register', [AuthController::class, 'register'])->middleware('role:Admin');
    Route::post('/logout', [AuthController::class, 'logout']);


    //EXPENSES API
    Route::get('/expenses', [ExpenseController::class, 'index']);
    Route::post('/expenses', [ExpenseController::class, 'create']);

    //Update expense - Manager or Admin
    Route::put('/expenses/{id}', [ExpenseController::class, 'update'])->middleware('role:Manager|Admin');

    // Delete expense - only Admins
    Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy'])->middleware('role:Admin');    
});

//USERS API
Route::middleware(['auth:sanctum', 'role:Admin'])->group(function () {
    Route::get('/users', [UserController::class, 'index']);       // List users
    Route::post('/users', [UserController::class, 'store']);      // Add user
    Route::put('/users/{id}', [UserController::class, 'update']); // Update user role
});

Route::middleware(['auth:sanctum', 'role:Admin'])->get('/check', function () {
    return response()->json(['status' => 'ok', 'user' => auth()->user()]);
});