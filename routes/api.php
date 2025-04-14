<?php

use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/



// Authentication Routes
Route::post('login', LoginController::class);
Route::post('register', RegisterController::class);

// Authenticated Routes
Route::middleware('auth:sanctum')->group(function(){

    // Expense Routes
    Route::prefix('expenses')->controller(ExpenseController::class)->group(function(){
        Route::get('', 'index')->middleware('cache.response');
        Route::post('', 'store');
        Route::put('/{expense}', 'update')->middleware('role:admin,manager');
        Route::delete('/{expense}', 'destroy')->middleware('role:admin');
    });

    // User Routes
    Route::prefix('users')->controller(UserController::class)->group(function(){
        Route::get('', 'index')->middleware(['role:admin', 'cache.response']);
        Route::post('', 'store')->middleware('role:admin');
        Route::put('/{id}', 'update')->middleware('role:admin');
    });
});

