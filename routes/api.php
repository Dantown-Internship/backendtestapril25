<?php

use App\Models\User;
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


Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {

    // Expenses Management
    Route::get('/expenses', [ExpenseController::class, 'index']);
    Route::post('/expenses', [ExpenseController::class, 'store']);
  

    //Exclude the employee role, to allow only admin and manager roles.
    $allowedRoles = implode(',', array_diff(User::ROLES, [User::ROLE_EMPLOYEE]));

    Route::put('/expenses/{id}', [ExpenseController::class, 'update'])
    ->middleware('role:' . $allowedRoles);

    Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy'])
    ->middleware('role:'. User::ROLE_ADMIN);

    // User Management
    Route::middleware('role:'. User::ROLE_ADMIN)->group(function () {
        Route::resource('/users', UserController::class)->only(['index', 'store', 'update']);
    });
});

