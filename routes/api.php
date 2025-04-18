<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ExpenseController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public routes
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');


// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/me', [AuthController::class, 'me'])->name('me');

    // Resending Verification Email route
    Route::post('/email/verification-notification', [AuthController::class, 'resendVerificationEmail'])->name('verification.send');
});

//Protected routes that require email verification
Route::middleware('auth:sanctum', 'verified')->group(function () {
    // Role-based routes
    Route::middleware('role:Admin')->group(function () {
        Route::post('/users', [UserController::class, 'createUser'])->name('users.create');
        Route::get('/users', [UserController::class, 'getUsers'])->name('users.list');
        Route::put('/users/{userId}', [UserController::class, 'updateUser'])->name('users.update');
        Route::delete('/expenses/{expenseId}', [ExpenseController::class, 'deleteExpense'])->name('expenses.delete');
    });

    Route::middleware('role:Admin,Manager')->group(function () {
        Route::put('/expenses/{expenseId}', [ExpenseController::class, 'updateExpense'])->name('expenses.update');
    });

    // Routes accessible by all authenticated and verified users
    Route::get('/expenses', [ExpenseController::class, 'getExpenses'])->name('expenses.list');
    Route::post('/expenses', [ExpenseController::class, 'createExpense'])->name('expenses.create');
    Route::get('/my-profile', [AuthController::class, 'getProfile'])->name('myprofile');
});
