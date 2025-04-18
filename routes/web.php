<?php

use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return response()->json([
        'message' => 'This is an API-only application. Please use the API endpoints.',
        'documentation' => 'Check the README for API documentation.'
    ]);
})->name('home');

// Login route - Now returns JSON indicating authentication required
// This route is typically the target for redirects from the Authenticate middleware
// for non-JSON requests. Changing it to return JSON might alter expected behavior
// if web-based authentication flows rely on this redirect.
Route::get('/login', function () {
    // Return JSON response indicating authentication is required instead of redirecting
    return response()->json(['message' => 'Unauthenticated.'], 401);
})->name('login');

// Test API route in web.php
Route::get('/api-test', function () {
    return response()->json([
        'message' => 'API test route is working!',
        'status' => 'success'
    ]);
});
