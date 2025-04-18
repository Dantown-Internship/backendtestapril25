<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, "register"])->middleware('auth:sanctum');
Route::post('/login', [AuthController::class, "login"]);