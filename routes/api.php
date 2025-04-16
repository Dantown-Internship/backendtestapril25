<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ExpenseController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/register', [AuthController::class, 'register'])->middleware('auth:sanctum', 'role:Admin');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);



Route::middleware('auth:sanctum')->group(function () {
    Route::get('/expenses', [ExpenseController::class, 'index']);
    Route::post('/expenses', [ExpenseController::class, 'store']);
    Route::put('/expenses/{id}', [ExpenseController::class, 'update'])->middleware('role:Admin,Manager');
    Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy'])->middleware('role:Admin');

    Route::middleware('role:Admin')->group(function () {
        Route::get('/users', [UserController::class, 'show']);
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/{id}', [UserController::class, 'update']);
    });

    Route::post('/logout', [AuthController::class, 'logout']);
});
