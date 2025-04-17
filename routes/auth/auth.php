<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

Route::post('/signin', [AuthController::class, 'signin']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/signup', [AuthController::class, 'signup']);
    Route::get('/me', [AuthController::class, 'me']);
    
});
