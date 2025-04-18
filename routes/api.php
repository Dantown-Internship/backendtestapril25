<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\RoleMiddleware;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login'])->middleware( 'throttle:10,60');
Route::post('/register', [AuthController::class, 'register'])->middleware('auth:sanctum', 'role:Administrator', 'throttle:5,60');

Route::middleware('auth:sanctum')->group(function () {
    // Protected routes will go here in later steps
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Expense Management Routes
    Route::get('/expenses', [ExpenseController::class, 'index']);
    Route::post('/expenses', [ExpenseController::class, 'store']);
    Route::put('/expenses/{expense}', [ExpenseController::class, 'update'])->middleware('role:Manager,Administrator');
    Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->middleware('role:Administrator');

    // User Management Routes
    Route::get('/users', [UserController::class, 'index'])->middleware('role:Administrator');
    Route::post('/users', [UserController::class, 'store'])->middleware('role:Administrator');
    Route::put('/users/{user}', [UserController::class, 'update'])->middleware('role:Administrator');

});