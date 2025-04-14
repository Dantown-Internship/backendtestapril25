<?php

use Illuminate\Support\Facades\Route;

Route::prefix('authentication')->group(__DIR__.'/AuthenticationRoutes.php');
Route::prefix('user-management')->group(__DIR__.'/UserManagementRoutes.php');