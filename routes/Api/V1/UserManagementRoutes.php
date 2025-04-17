<?php

use App\Http\Controllers\V1\UserManagement\CreateUserController;
use App\Http\Controllers\V1\UserManagement\FetchUsersController;
use App\Http\Controllers\V1\UserManagement\UpdateUserController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['auth:sanctum', 'adminOnlyAuthorization']
], function () {
    Route::put('/users/{userId}', UpdateUserController::class);

    Route::post('/users', CreateUserController::class);

    Route::get('/users', FetchUsersController::class);
});
