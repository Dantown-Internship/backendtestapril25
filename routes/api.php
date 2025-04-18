<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/register', [RegisteredUserController::class, 'store']); // An user creates a new company and is given the admin role
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/expenses', [ExpenseController::class, 'index']);
        Route::post('/expenses', [ExpenseController::class, 'store']);
        Route::put('/expenses/{id}', [ExpenseController::class, 'update'])->middleware('role:Admin,Manager');
        Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy'])->middleware('role:Admin');

        Route::middleware('role:Admin')->group(function () {
            Route::get('/users', [UserController::class, 'index']);
            Route::post('/users', [UserController::class, 'store']);
            Route::put('/users/{id}', [UserController::class, 'update']);
        });
    });
});
