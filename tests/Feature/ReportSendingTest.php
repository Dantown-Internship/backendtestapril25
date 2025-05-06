<?php

use App\Jobs\SendCompanyWeeklyExpenseReportJob;
use App\Mail\WeeklyExpenseReportMail;
use App\Models\Company;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('sends weekly expense email to company admins', function () {
    Mail::fake();

    $company = Company::factory()->create();
    $admin1 = User::factory()->create(['company_id' => $company->id, 'role' => 'Admin']);
    $admin2 = User::factory()->create(['company_id' => $company->id, 'role' => 'Admin']);

    $employeeUsers = User::factory()->count(10)->create(['company_id' => $company->id, 'role' => 'Employee']);

    foreach ($employeeUsers as $user) {
        Expense::factory()->count(10)->create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'created_at' => now(),
        ]);
    }

    (new SendCompanyWeeklyExpenseReportJob($company))->handle();

    Mail::assertQueued(WeeklyExpenseReportMail::class, 2);
});
