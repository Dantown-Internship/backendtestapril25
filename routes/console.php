<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\SendExpenseReportJob;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new SendExpenseReportJob)->everyMinute()
->before(function () {
    Log::info('SendExpenseReportJob is about to run.');
})
->after(function () {
    Log::info('SendExpenseReportJob finished running.');
});