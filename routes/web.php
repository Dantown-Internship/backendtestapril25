<?php

use App\Mail\WeeklyExpenseReport;
use App\Models\Expense;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/mail/test-weekly-report', function () {

    $companyId = auth()->id() ? auth()->user()->company_id : '01964991-4322-70fb-a605-9323620d9cf9';
    $expenses = Expense::where('company_id', $companyId)
        ->whereDate('created_at', now()->toDateString())
        ->get();

    Mail::to('andreweaglepire@gmail.com')
        ->send(new WeeklyExpenseReport($expenses));

    return 'WeeklyExpenseReport sent with ' . $expenses->count() . ' items';
});
