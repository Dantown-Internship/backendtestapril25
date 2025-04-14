<?php

// authentication routes

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExpensesController;
use App\Http\Controllers\UserController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


// expenses routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/expenses', [ExpensesController::class, 'index']);
    Route::post('/expenses', [ExpensesController::class, 'store']);
    Route::put('/expenses/{id}', [ExpensesController::class, 'update'])->middleware('role:Admin,Manager');
    Route::delete('/expenses/{id}', [ExpensesController::class, 'destroy'])->middleware('role:Admin');

    // user mgt routes
    Route::get('/users', [UserController::class, 'index'])->middleware('role:Admin');
    Route::post('/users', [UserController::class, 'store'])->middleware('role:Admin');
    Route::put('/users/{id}', [UserController::class, 'updateRole'])->middleware('role:Admin');
});


// Route::middleware(['auth:sanctum', 'role:Admin'])->group(function () {
//     Route::get('/users', [UserController::class, 'index']);
//     Route::post('/users', [UserController::class, 'store']);
// });

// Route::middleware(['auth:sanctum', 'role:Admin,Manager'])->group(function () {
//     Route::put('/expenses/{id}', [ExpenseController::class, 'update']);
// });

// Route::middleware(['auth:sanctum', 'role:Admin,Manager,Employee'])->group(function () {
//     Route::get('/expenses', [ExpenseController::class, 'index']);
// });
