<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;


Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout');
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('expenses', ExpenseController::class);
    Route::apiResource('users', UserController::class)->only(['index', 'store', 'update']);
});
