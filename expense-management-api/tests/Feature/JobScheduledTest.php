<?php

namespace Tests\Feature;

use App\Jobs\SendWeeklyExpenseReport;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class JobScheduledTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    public function test_weekly_expense_report_command_dispatches_job(): void
    {
        // prevent actual job dispatch
        Bus::fake();

        // run the command
        Artisan::call('reports:weekly');

        // check if job was dispatched
        Bus::assertDispatched(SendWeeklyExpenseReport::class);
    }


    public function test_weekly_expense_report_command_is_scheduled(): void
    {
        $schedule = app(Schedule::class);

        $events = collect($schedule->events());

        $weeklyReportCommand = $events->first(function ($event) {
            return str_contains($event->command, 'reports:weekly');
        });

        $this->assertNotNull($weeklyReportCommand, 'Weekly report command is not scheduled');
        $this->assertTrue($weeklyReportCommand->expression === '0 8 * * 1');
    }
}
