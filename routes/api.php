<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// routes/api.php

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    //Authenticated users
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        //Expenses create
        Route::get('/expenses', [ExpenseController::class, 'index']);
        Route::post('/expenses', [ExpenseController::class, 'create']);

        Route::middleware(['role:Admin, Manager'])->group(function () {
            Route::put('/expenses/{expense}', [ExpenseController::class, 'update']);
        });

        Route::middleware(['role:Admin'])->group(function () {
            Route::delete('/expenses/{expense}', [ExpenseController::class, 'delete']);
            Route::get('/users', [UserController::class, 'listUser']);
            Route::post('/users', [UserController::class, 'addUser']);
            Route::put('/users/{user}', [UserController::class, 'update']);

            Route::get('/audith-logs', [UserController::class, 'audith']);
        });
    });
});


