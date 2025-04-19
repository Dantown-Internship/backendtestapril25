<?php

use App\Jobs\SendExpenseReportJob;
use App\Models\Company;
use App\Models\Expense;
use App\Models\User;
use App\Notifications\WeeklyExpenseReportNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;
use App\Enums\RoleEnum;
use Illuminate\Support\Facades\Bus;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    // Seed roles and permissions
    $this->artisan('db:seed', ['--class' => 'Database\Seeders\RolesAndPermissionsSeeder']);

    // Create test companies
    $this->companyA = Company::factory()->create(['name' => 'Company A']);
    $this->companyB = Company::factory()->create(['name' => 'Company B']);

    // Create admin users
    $this->adminA = User::factory()->create([
        'company_id' => $this->companyA->id,
        'role' => RoleEnum::ADMIN->value,
        'email' => 'admin@companya.com',
    ]);
    $this->adminA->assignRole('admin');

    $this->adminB = User::factory()->create([
        'company_id' => $this->companyB->id,
        'role' => RoleEnum::ADMIN->value,
        'email' => 'admin@companyb.com',
    ]);
    $this->adminB->assignRole('admin');

    // Create employees
    $this->employeeA = User::factory()->create([
        'company_id' => $this->companyA->id,
        'role' => RoleEnum::EMPLOYEE->value,
    ]);
    $this->employeeA->assignRole('employee');

    // Create expenses for last week
    $this->lastWeekStart = now()->subWeek()->startOfWeek();
    $this->lastWeekEnd = now()->subWeek()->endOfWeek();

    Expense::factory()->count(5)->create([
        'company_id' => $this->companyA->id,
        'user_id' => $this->employeeA->id,
        'category' => 'Travel',
        'created_at' => $this->lastWeekStart->copy()->addDays(2),
        'amount' => 100,
    ]);

    Expense::factory()->count(3)->create([
        'company_id' => $this->companyA->id,
        'user_id' => $this->employeeA->id,
        'category' => 'Office Supplies',
        'created_at' => $this->lastWeekStart->copy()->addDays(3),
        'amount' => 50,
    ]);

    Expense::factory()->count(4)->create([
        'company_id' => $this->companyB->id,
        'user_id' => $this->adminB->id,
        'category' => 'Meals',
        'created_at' => $this->lastWeekStart->copy()->addDays(1),
        'amount' => 75,
    ]);
});

test('it dispatches expense report job for admin users', function () {
    Bus::fake();

    // Call the command handler
    $this->artisan('app:send-weekly-expense-reports')
        ->assertSuccessful();

    // Assert that the SendExpenseReportJob was dispatched with admin user IDs
    Bus::assertDispatched(SendExpenseReportJob::class, function ($job) {
        // Should include both admin users' IDs
        return in_array($this->adminA->id, $job->userIds) &&
            in_array($this->adminB->id, $job->userIds) &&
            !in_array($this->employeeA->id, $job->userIds);
    });
});

test('it sends notifications only to admins with personal expenses', function () {
    Notification::fake();

    // Dispatch the job with admin IDs
    $job = new SendExpenseReportJob([$this->adminA->id, $this->adminB->id]);
    $job->handle();

    // AdminB has personal expenses and should receive a notification
    Notification::assertSentTo(
        $this->adminB,
        WeeklyExpenseReportNotification::class,
        function ($notification) {
            // AdminB's personal expenses: 4 meals at $75 each = $300
            expect($notification->totalAmount)->toBe(300.0);
            expect($notification->categoryTotals)->toHaveKey('Meals');
            expect($notification->categoryTotals['Meals'])->toBe(300);
            expect($notification->lastWeekStart->format('Y-m-d'))->toBe($this->lastWeekStart->format('Y-m-d'));
            expect($notification->lastWeekEnd->format('Y-m-d'))->toBe($this->lastWeekEnd->format('Y-m-d'));

            // Verify the expenses are only for this admin (userExpenses filtering in job)
            $expenseUserIds = collect($notification->userExpenses)->pluck('user_id')->unique();
            expect($expenseUserIds->count())->toBe(1);
            expect($expenseUserIds->first())->toBe($this->adminB->id);

            return true;
        }
    );

    // AdminA doesn't have personal expenses and should NOT get any notification
    Notification::assertNotSentTo($this->adminA, WeeklyExpenseReportNotification::class);
});

test('it handles chunk processing correctly with multiple admins', function () {
    Notification::fake();

    // Create 25 more admin users (to test chunking)
    $additionalAdmins = User::factory()->count(25)->create([
        'role' => RoleEnum::ADMIN->value,
        'company_id' => $this->companyA->id,
    ]);

    // Set up user IDs array
    $adminIds = [$this->adminA->id, $this->adminB->id];
    foreach ($additionalAdmins as $admin) {
        $adminIds[] = $admin->id;
        $admin->assignRole('admin');
    }

    // Create some expenses for random admins
    foreach ($additionalAdmins as $index => $admin) {
        if ($index % 3 === 0) {
            Expense::factory()->create([
                'company_id' => $this->companyA->id,
                'user_id' => $admin->id,
                'category' => 'Hardware',
                'created_at' => $this->lastWeekStart->copy()->addDays(2),
                'amount' => 200,
            ]);
        }
    }

    // Dispatch the job
    $job = new SendExpenseReportJob($adminIds);
    $job->handle();

    // Verify notifications were sent to the correct admins
    $adminsWithNotifications = 0;

    // AdminB should get notification (has personal meals expenses)
    Notification::assertSentTo($this->adminB, WeeklyExpenseReportNotification::class);
    $adminsWithNotifications++;

    // AdminA should not get notification (no personal expenses)
    Notification::assertNotSentTo($this->adminA, WeeklyExpenseReportNotification::class);

    // Check each additional admin
    foreach ($additionalAdmins as $index => $admin) {
        if ($index % 3 === 0) {
            // Should get notification (has personal expenses)
            Notification::assertSentTo($admin, WeeklyExpenseReportNotification::class);
            $adminsWithNotifications++;
        } else {
            // Should not get notification (no personal expenses)
            Notification::assertNotSentTo($admin, WeeklyExpenseReportNotification::class);
        }
    }

    // Ensure we have the expected number of notifications
    // 1 for adminB + approximately 8-9 additional admins (every 3rd one out of 25)
    expect($adminsWithNotifications)->toBeGreaterThanOrEqual(9);
    expect($adminsWithNotifications)->toBeLessThanOrEqual(10);
});

test('it filters expenses specific to each user', function () {
    Notification::fake();

    // Create another admin with expenses
    $adminC = User::factory()->create([
        'company_id' => $this->companyA->id,
        'role' => RoleEnum::ADMIN->value,
    ]);
    $adminC->assignRole('admin');

    // Add expenses for this admin
    Expense::factory()->count(2)->create([
        'company_id' => $this->companyA->id,
        'user_id' => $adminC->id,
        'category' => 'Entertainment',
        'created_at' => $this->lastWeekStart->copy()->addDays(4),
        'amount' => 150,
    ]);

    // Dispatch job for both admins
    $job = new SendExpenseReportJob([$this->adminB->id, $adminC->id]);
    $job->handle();

    // Verify each admin gets only their own expenses
    Notification::assertSentTo(
        $this->adminB,
        WeeklyExpenseReportNotification::class,
        function ($notification) {
            // Should only have Meals category (adminB's expenses)
            expect($notification->categoryTotals)->toHaveKey('Meals');
            expect($notification->totalAmount)->toBe(300.0);
            return true;
        }
    );

    Notification::assertSentTo(
        $adminC,
        WeeklyExpenseReportNotification::class,
        function ($notification) {
            // Should only have Entertainment category (adminC's expenses)
            expect($notification->categoryTotals)->toHaveKey('Entertainment');
            expect($notification->totalAmount)->toBe(300.0); // 2 entertainment at $150
            return true;
        }
    );
});
