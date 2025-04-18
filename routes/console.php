<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use App\Jobs\SendWeeklyExpenseReportJob;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::call(function () {
    $admins = User::where('role', 'Admin')->get();

    foreach ($admins as $admin) {
        SendWeeklyExpenseReportJob::dispatch($admin);
    }
})->weeklyOn(1, '08:00'); // Every Monday at 8 AM

