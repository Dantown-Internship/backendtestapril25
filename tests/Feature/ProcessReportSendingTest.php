<?php

use App\Jobs\ProcessWeeklyExpenseReportSending;
use App\Jobs\SendCompanyWeeklyExpenseReportJob;
use App\Models\Company;
use Illuminate\Support\Facades\Bus;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('dispatches ProcessWeeklyExpenseReportSending works', function () {
    Bus::fake();

    Company::factory()->count(5)->hasUsers(200)->create();

    (new ProcessWeeklyExpenseReportSending)->handle();

    Bus::assertDispatched(SendCompanyWeeklyExpenseReportJob::class);
});
