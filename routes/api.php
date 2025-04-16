<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExpensesManangementController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');


Route::post('login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {

    Route::middleware(['role:Admin'])->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::delete('user/{id}', [AuthController::class, 'deleteUser']);
        Route::get('users', [AuthController::class, 'listUsers']);
        Route::delete('expenses/{id}', [ExpensesManangementController::class, 'deleteExpense']);
        Route::put('users/{id}', [AuthController::class, 'updateUserRole']);
    });

    Route::put('expenses/{id}', [ExpensesManangementController::class, 'updateExpenses'])->middleware('adminOrManager');

    Route::post('expenses', [ExpensesManangementController::class, 'creatExpenses']);
    Route::get('expenses', [ExpensesManangementController::class, 'getExpenses']);
    Route::post('companies', [ExpensesManangementController::class, 'createCompany']);
});




