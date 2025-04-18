<?php

use App\Jobs\SendExpenseReportJob;
use App\Models\Company;
use App\Models\Expense;
use App\Models\User;
use App\Notifications\WeeklyExpenseReportNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    // Migration fresh before each test
    $this->artisan('migrate:fresh');

    // Seed roles and permissions
    $this->artisan('db:seed', ['--class' => 'Database\Seeders\RolesAndPermissionsSeeder']);

    // Create test companies
    $this->companyA = Company::factory()->create(['name' => 'Company A']);
    $this->companyB = Company::factory()->create(['name' => 'Company B']);

    // Create admin users
    $this->adminA = User::factory()->create([
        'company_id' => $this->companyA->id,
        'role' => 'Admin',
        'email' => 'admin@companya.com',
    ]);
    $this->adminA->assignRole('admin');

    $this->adminB = User::factory()->create([
        'company_id' => $this->companyB->id,
        'role' => 'Admin',
        'email' => 'admin@companyb.com',
    ]);
    $this->adminB->assignRole('admin');

    // Create employees
    $this->employeeA = User::factory()->create([
        'company_id' => $this->companyA->id,
        'role' => 'Employee',
    ]);
    $this->employeeA->assignRole('employee');

    // Create expenses for last week
    $lastWeekStart = now()->subWeek()->startOfWeek();

    Expense::factory()->count(5)->create([
        'company_id' => $this->companyA->id,
        'user_id' => $this->employeeA->id,
        'category' => 'Travel',
        'created_at' => $lastWeekStart->copy()->addDays(2),
    ]);

    Expense::factory()->count(3)->create([
        'company_id' => $this->companyA->id,
        'user_id' => $this->employeeA->id,
        'category' => 'Office Supplies',
        'created_at' => $lastWeekStart->copy()->addDays(3),
    ]);

    Expense::factory()->count(4)->create([
        'company_id' => $this->companyB->id,
        'user_id' => $this->adminB->id,
        'category' => 'Meals',
        'created_at' => $lastWeekStart->copy()->addDays(1),
    ]);
});

test('weekly expense report command dispatches jobs for all company admins', function () {
    Queue::fake();

    $this->artisan('reports:weekly')
        ->assertSuccessful();

    // Verify that a job was dispatched for each admin
    Queue::assertPushed(SendExpenseReportJob::class, function ($job) {
        return $job->user->id === $this->adminA->id;
    });

    Queue::assertPushed(SendExpenseReportJob::class, function ($job) {
        return $job->user->id === $this->adminB->id;
    });

    // There should be exactly 2 jobs (one for each admin)
    Queue::assertPushed(SendExpenseReportJob::class, 2);
});

test('expense report job sends email with correct data', function () {
    Notification::fake();

    // Manually dispatch the job
    $job = new SendExpenseReportJob($this->adminA);
    $job->handle();

    // Verify that an email was sent to the admin
    Notification::assertSent(WeeklyExpenseReportNotification::class, function ($mail) {
        return $mail->hasTo($this->adminA->email);
    });

    // Verify the email content
    Notification::assertSent(WeeklyExpenseReportNotification::class, function ($mail) {
        // Check if the user property is correct
        if ($mail->user->id !== $this->adminA->id) {
            return false;
        }

        // Check if the expenses count is correct (8 for Company A)
        if (count($mail->expenses) !== 8) {
            return false;
        }

        // Check if the category totals contain both categories
        $hasTravel = false;
        $hasOfficeSupplies = false;

        foreach ($mail->categoryTotals as $category => $amount) {
            if ($category === 'Travel') {
                $hasTravel = true;
            }
            if ($category === 'Office Supplies') {
                $hasOfficeSupplies = true;
            }
        }

        return $hasTravel && $hasOfficeSupplies;
    });
});
