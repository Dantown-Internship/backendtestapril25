<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    /*
     * Authentication Routes
     */
    Route::post('/register', [AuthController::class, 'register']); 
    Route::post('/login', [AuthController::class, 'login']);      


    /*
     * Protected API Routes
     */
    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('expenses', ExpenseController::class)->only([
            'index',   
            'store',  
            'update',  
            'destroy'  
        ]);

        Route::apiResource('expenses', ExpenseController::class)->except('update', 'destroy');

    Route::middleware('role:Admin,Manager')->group(function () {
        Route::put('/expenses/{expense}', [ExpenseController::class, 'update']);
    });

    Route::middleware('role:Admin')->group(function () {
        Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy']);
    });

    Route::middleware('role:Admin')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/{user}', [UserController::class, 'update']);
    });
    });
});