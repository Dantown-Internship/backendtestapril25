<?php
// ✅ Feature Test 6: Weekly Report Email to Admins
// // This test will check if the weekly report email is sent to all company admins.

use App\Models\Company;
use App\Models\User;
use App\Models\Expense;
use App\Enums\Roles;
use Illuminate\Support\Facades\Mail;
use App\Mail\WeeklyReportEmail;
use App\Jobs\WeeklyExpenseReport;

it('sends a weekly report email to admins with correct expenses', function () {
    Mail::fake();

    $company = Company::factory()->create();

    // Create admin user
    $admin = User::factory()->create([
        'company_id' => $company->id,
        'role' => Roles::ADMIN,
        'email' => 'admin@company.com',
    ]);

    // Create expenses for last week
    $lastWeek = now()->subWeek();
    $expenses = Expense::factory()->count(3)->create([
        'company_id' => $company->id,
        'user_id' => $admin->id,
        'created_at' => $lastWeek->copy()->startOfWeek()->addDays(1), // inside last week
    ]);

    // Run the job
    (new WeeklyExpenseReport())->handle();

    // Assert the email was sent
    Mail::assertSent(WeeklyReportEmail::class, function ($mail) use ($admin, $company, $expenses) {
        return $mail->hasTo($admin->email)
            && $mail->company->is($company)
            && count($mail->expenses) === 3;
    });
});