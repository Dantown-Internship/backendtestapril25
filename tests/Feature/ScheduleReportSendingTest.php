<?php

use App\Jobs\ProcessWeeklyExpenseReportSending;
use Illuminate\Console\Scheduling\Schedule;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('schedules ProcessWeeklyExpenseReportSending job correctly', function () {
    $events = app(Schedule::class)->events();

    $jobScheduled = collect($events)->first(function ($event) {
        return str($event->description)->contains(ProcessWeeklyExpenseReportSending::class)
            && $event->expression === '0 8 * * 1'
            && $event->timezone === 'Africa/Lagos';
    });

    expect($jobScheduled)->not()->toBeNull();
});
