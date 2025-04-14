<?php

use App\Http\Controllers\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', \App\Http\Controllers\Api\Auth\LoginController::class)->name('login');

// Route::post('/register', RegisterController::class)->name('register');