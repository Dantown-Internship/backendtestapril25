<?php


use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExpensesController;
use App\Http\Controllers\UserController;


// authentication routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->middleware('throttle:5,1');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:5,1');
Route::get('/verify-email/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware('signed')
    ->name('verification.verify');

    Route::middleware('auth:sanctum')->post('/refresh-token', [AuthController::class, 'refreshToken']);


// expenses routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/expenses', [ExpensesController::class, 'listExpenses']);
    Route::post('/expenses', [ExpensesController::class, 'saveExpenses']);
    Route::put('/expenses/{id}', [ExpensesController::class, 'updateExpenses'])->middleware('role:Admin,Manager');
    Route::delete('/expenses/{id}', [ExpensesController::class, 'destroyExpenses'])->middleware('role:Admin');

    // user mgt routes
    Route::get('/users', [UserController::class, 'listUsers'])->middleware('role:Admin');
    Route::post('/users', [UserController::class, 'storeUsersData'])->middleware('role:Admin');
    Route::put('/users/{id}', [UserController::class, 'updateRole'])->middleware('role:Admin');
});


// Route::middleware(['auth:sanctum', 'role:Admin'])->group(function () {
//     Route::get('/users', [UserController::class, 'index']);
//     Route::post('/users', [UserController::class, 'store']);
// });

// Route::middleware(['auth:sanctum', 'role:Admin,Manager'])->group(function () {
//     Route::put('/expenses/{id}', [ExpenseController::class, 'update']);
// });

// Route::middleware(['auth:sanctum', 'role:Admin,Manager,Employee'])->group(function () {
//     Route::get('/expenses', [ExpenseController::class, 'index']);
// });
