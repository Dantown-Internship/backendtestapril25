<?php

use App\Jobs\SendWeeklyReport;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


// schedule job
Schedule::command("app:send-weekly-expense-reports-command")->weekly()->fridays()->at('09:00');
