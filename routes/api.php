<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ExpenseController;
use App\Http\Middleware\RoleMiddleware;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register'])->middleware(['auth:sanctum', RoleMiddleware::class . ':Admin']);

Route::middleware('auth:sanctum')->group(function () {
    
    Route::get('/expenses', [ExpenseController::class, 'index']);
    Route::post('/expenses', [ExpenseController::class, 'store'])->middleware(RoleMiddleware::class . ':Admin,Manager,Employee');
    Route::put('/expenses/{expense}', [ExpenseController::class, 'update'])->middleware(RoleMiddleware::class . ':Admin,Manager');
    Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->middleware(RoleMiddleware::class . ':Admin');

    Route::get('/users', [UserController::class, 'index'])->middleware(RoleMiddleware::class . ':Admin');
    Route::post('/users', [UserController::class, 'store'])->middleware(RoleMiddleware::class . ':Admin');
    Route::put('/users/{id}', [UserController::class, 'update'])->middleware(RoleMiddleware::class . ':Admin');
});
