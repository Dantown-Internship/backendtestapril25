<?php

use App\Enums\Roles;
use App\Jobs\WeeklyExpenseReportJob;
use App\Mail\WeeklyExpenseMail;
use App\Models\Company;
use App\Models\Expense;
use App\Models\User;
use App\Services\PdfService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    Mail::fake();
    Storage::fake('public');
    Storage::fake('local');
});

test('weekly expense report job generates pdf and sends email', function () {

    $company = Company::factory()->create();

    $admin = User::factory()->create([
        'company_id' => $company->id,
        'role'       => Roles::ADMIN->value,
    ]);

    // Create some expenses
    Expense::factory()->count(3)->create([
        'company_id' => $company->id,
        'user_id'    => $admin->id,
        'created_at' => now()->subDays(5),
    ]);

    // Run the job
    $job = new WeeklyExpenseReportJob();
    $job->handle();

    // Assert that the email was sent to admin
    Mail::assertSent(WeeklyExpenseMail::class, function ($mail) use ($admin) {
        return $mail->hasTo($admin->email);
    });
});
