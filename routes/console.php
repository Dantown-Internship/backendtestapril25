<?php

use App\Jobs\BackgroundProcessing\Expenses\SendWeeklyExpenseReportBackgroundProcessingJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('cronjob:send-weekly-expense-report', function() {
    dispatch(
        new SendWeeklyExpenseReportBackgroundProcessingJob()
    );
})->purpose('Cronjob to send weekly expense reports');