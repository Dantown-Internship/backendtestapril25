<?php

use App\Jobs\ExpenseReport;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new ExpenseReport)->weekly()->sundays()->at('10:00')->timezone('Africa/Lagos')
    ->onSuccess(function () {
        $this->info('Expense report job executed successfully.');
    })
    ->onFailure(function () {
        $this->error('Expense report job failed to execute.');
    });
