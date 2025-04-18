<?php

use App\Jobs\DispatchCompanyExpenseReportJobs;
use App\Models\User;
use App\Notifications\WeeklyExpenseReportNotification;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Notification;

test('weekly expense report is scheduled to run weekly', function () {
    $schedule = app(Schedule::class);

    $events = collect($schedule->events());

    $weeklyReportEvents = $events->filter(function ($event) {
        return str_contains($event->command, 'expense:report');
    });

    expect($weeklyReportEvents)->not->toBeEmpty('Weekly report job is not scheduled');
    $event = $weeklyReportEvents->first();
    expect($event->expression)->toBe('0 8 * * 1');
});

test('weekly expense report notification is sent to all the admins of the company', function () {
    Notification::fake();

    $admins = User::factory(10)->admin()->create();
    app(DispatchCompanyExpenseReportJobs::class)->handle();

    foreach ($admins as $admin) {
        Notification::assertSentTo($admin, WeeklyExpenseReportNotification::class);
    }
    Notification::assertCount($admins->count());
});
