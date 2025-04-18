<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('company', [CompanyController::class, 'createCompany'])
    // ->middleware('auth:sanctum')
    ->name('company.create');

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('users', [UserController::class, 'createUser'])
        ->name('user.create');
    Route::get('users', [UserController::class, 'getUsers'])
        ->name('user.get');
    Route::put('users/{id}', [UserController::class, 'updateUser'])
        ->name('user.update');

    Route::resource('expenses', ExpenseController::class)
        ->only(['index', 'store', 'show', 'update', 'destroy']);
});