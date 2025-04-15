<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ExpenseController;


Route::post('/v1/login', [AuthController::class, 'login'])
    ->middleware('guest');
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::apiResource('users', UserController::class);
    Route::apiResource('expenses', ExpenseController::class);
});
