<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\ApiAutoWrapper;
use App\Http\Middleware\ApiContentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::group(['prefix' => 'v1','middleware' => [ ApiContentType::class,ApiAutoWrapper::class ]],function (){
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']); // for admins

    Route::post('/companies',[ CompanyController::class,'store' ]);

    Route::middleware('auth:sanctum')->group(function () {

        // Expense Management Endpoints
        Route::get('/expenses', [ExpenseController::class, 'index']);
        Route::post('/expenses', [ExpenseController::class, 'store']);
        Route::put('/expenses/{id}', [ExpenseController::class, 'update'])->middleware('check-role:Admin,Manager');
        Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy'])->middleware('check-role:Admin');

        // User Management Endpoints (Admins only)
        Route::get('/users', [UserController::class, 'index'])->middleware('check-role:Admin');
        Route::post('/users', [UserController::class, 'store'])->middleware('check-role:Admin');
        Route::put('/users/{id}', [UserController::class, 'update'])->middleware('check-role:Admin');
    });
});





