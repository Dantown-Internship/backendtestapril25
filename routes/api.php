<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ExpenseController;

Route::post('/register', [AuthController::class, 'register'])->middleware('auth:sanctum');
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::apiResource('users', UserController::class)
        ->except(['show', 'destroy']);
    
    Route::apiResource('expenses', ExpenseController::class);
});

Route::get('/authenticated/json', function () {
    return response()->json(['message' => 'Unauthorized, please login'], 401);
})->name('api.authenticated.json');