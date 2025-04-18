<?php

use App\Jobs\SendWeeklyExpenseReport;

return function (Illuminate\Console\Scheduling\Schedule $schedule) {
    $schedule->job(new SendWeeklyExpenseReport)->weeklyOn(1, '08:00');
};