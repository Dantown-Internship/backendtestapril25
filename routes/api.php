<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\V1\TaskController;
use App\Http\Controllers\Api\V1\ExpenseController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']); 

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/expenses', [ExpenseController::class, 'index']);
    Route::post('/expenses', [ExpenseController::class, 'store'])->middleware('role:Admin,Manager,Employee');
    Route::put('/expenses/{id}', [ExpenseController::class, 'update'])->middleware('role:Admin,Manager');
    Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy'])->middleware('role:Admin');

    Route::middleware('role:Admin')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/{id}', [UserController::class, 'update']);
    });
});