<?php
use Illuminate\Console\Scheduling\Schedule;
use App\Jobs\SendExpenseReportToAdmins;
use App\Jobs\SendWeeklyExpenseReport;

return function (Schedule $schedule) {
    $schedule->job(new SendWeeklyExpenseReport)
             ->weekly()
             ->mondays()
             ->at('08:00');
};
