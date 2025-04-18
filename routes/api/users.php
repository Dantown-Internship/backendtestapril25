<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [UserController::class, "index"]);
    Route::post('/users', [UserController::class, "store"]);
    Route::middleware('ensure.company.match:user')->group(function () {
        Route::put('/users/{user}', [UserController::class, "update"]);
    });
});
