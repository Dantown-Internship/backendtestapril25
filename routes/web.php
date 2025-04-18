<?php

use App\Mail\WeeklyExpenseReport;
use Illuminate\Support\Facades\Mail;
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
    return view('welcome');
});

Route::get('/test-mail', function () {
    $user = \App\Models\User::first();
    $expenses = \App\Models\Expense::where('company_id', $user->company_id)->get();

    Mail::to($user->email)->send(new WeeklyExpenseReport($user, $expenses));

    return 'Mail sent';
});
