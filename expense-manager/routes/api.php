<?php

use App\Http\Controllers\AuthController;


Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    //Admin on registercd 
    Route::post('/register', [AuthController::class, 'register'])->middleware('role:Admin');
    Route::post('/logout', [AuthController::class, 'logout']);
});
