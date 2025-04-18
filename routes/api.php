<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\UserController;

// Route::post('/register', [AuthController::class, 'register'])->middleware('role:Admin,Manager');
Route::post('/register', [AuthController::class, 'register'])->middleware(['auth:sanctum',  \App\Http\Middleware\CheckRole::class . ':Admin']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);



Route::middleware(['auth:sanctum', \App\Http\Middleware\EnsureTenantAccess::class . ':tenant', \App\Http\Middleware\CheckRole::class . ':Admin'])->group(function () {
    Route::get('/expenses', [ExpenseController::class, 'index']);
    Route::post('/expenses', [ExpenseController::class, 'store']);
    Route::put('/expenses/{expense}', [ExpenseController::class, 'update'])->middleware(\App\Http\Middleware\CheckRole::class . ':Admin,Manager'); 
    Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->middleware(\App\Http\Middleware\CheckRole::class . ':Admin');
    Route::get('/users', [UserController::class, 'index'])->middleware(\App\Http\Middleware\CheckRole::class . ':Admin');
    Route::post('/users', [UserController::class, 'store'])->middleware(\App\Http\Middleware\CheckRole::class . ':Admin');
    Route::put('/users/{id}', [UserController::class, 'update'])->middleware(\App\Http\Middleware\CheckRole::class . ':Admin');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->middleware(\App\Http\Middleware\CheckRole::class . ':Admin');
});

Route::get('/middleware-test', function () {
    return 'Middleware is working!';
})->middleware('role:Admin');
