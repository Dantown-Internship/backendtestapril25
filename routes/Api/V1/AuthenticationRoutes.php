<?php

use App\Http\Controllers\V1\Authentication\LoginController;
use App\Http\Controllers\V1\Authentication\OnboardingController;
use Illuminate\Support\Facades\Route;

Route::post('/register', OnboardingController::class);
Route::post('/login', LoginController::class);