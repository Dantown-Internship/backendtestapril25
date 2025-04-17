<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


// authenticated routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/users', [UserController::class, 'createUser'])->middleware('check-role:Admin');
    Route::get('/users', [UserController::class, 'getListOfUsers'])->middleware('check-role:Admin');
    Route::put('/users/{id}', [UserController::class, 'updateUserRole'])->middleware('check-role:Admin');
    Route::post('/expenses', [ExpenseController::class, 'store']);
    Route::get('/expenses', [ExpenseController::class, 'index']);
    Route::put('/expenses/{id}', [ExpenseController::class, 'update']);
    Route::delete('/expenses/{id}', [ExpenseController::class, 'delete']);
});