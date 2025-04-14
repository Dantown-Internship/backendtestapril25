<?php

use App\Http\Controllers\V1\Admin\UserManagement\CreateUserController;
use App\Http\Controllers\V1\Admin\UserManagement\FetchUsersController;
use App\Http\Controllers\V1\Admin\UserManagement\UpdateUserController;
use Illuminate\Support\Facades\Route;

Route::group([], function () {
    Route::post('/users', CreateUserController::class);
    Route::get('/users', FetchUsersController::class);
    Route::put('/users/{userId}', UpdateUserController::class);
});
