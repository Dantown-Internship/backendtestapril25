<?php

use App\Http\Controllers\V1\Shared\Authentication\LoginController;
use App\Http\Controllers\V1\Shared\Authentication\OnboardingController;
use Illuminate\Support\Facades\Route;

Route::post('/register', OnboardingController::class);
Route::post('/login', LoginController::class);