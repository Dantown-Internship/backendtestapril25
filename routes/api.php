<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\UserController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/expenses', [ExpenseController::class, 'index']);
    Route::post('/expenses', [ExpenseController::class, 'store'])->middleware('role:Admin,Manager,Employee');
    Route::put('/expenses/{id}', [ExpenseController::class, 'update'])->middleware('role:Admin,Manager');
    Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy'])->middleware('role:Admin');

    Route::get('/users', [UserController::class, 'index'])->middleware('role:Admin');
    Route::post('/users', [UserController::class, 'store'])->middleware('role:Admin');
    Route::put('/users/{id}', [UserController::class, 'update'])->middleware('role:Admin');
});

Route::fallback(function () {
    return response()->json(['message' => 'API Route Not Found'], 404);
});
