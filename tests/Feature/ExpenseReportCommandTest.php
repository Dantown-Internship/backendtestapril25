<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\Company;
use App\Models\Expense;
use App\Models\User;
use App\Notifications\ReportNotification;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ExpenseReportCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_expense_report_command_sends_notifications()
    {
        Storage::fake('public');
        Notification::fake();

        $company = Company::factory()->create(['name' => 'Acme Corp']);

        $admin = User::factory()->create([
            'company_id' => $company->id,
            'role' => RoleEnum::ADMIN,
        ]);

        Expense::factory()->count(3)->create([
            'company_id' => $company->id,
            'created_at' => Carbon::now()->subDays(3),
        ]);

        $this->artisan('app:expense-report')
            ->expectsOutput('*** initializing expense report ***')
            ->expectsOutput('<<< searching for companies >>>')
            ->expectsOutput("<<< users and expenses for {$company->name} found >>>")
            ->expectsOutput("---- generating expense report file for {$company->name} --")
            ->expectsOutput("----- file generation done for {$company->name} --")
            ->expectsOutput("------ emailing report to {$admin->name}")
            ->expectsOutput('Weekly Expenses Done')
            ->assertExitCode(0);

        $fileName = 'reports/' . str_replace(' ', '', $company->name) . '_weekly_expenses.xlsx';
        $this->assertTrue(Storage::disk('public')->exists($fileName));

        Notification::assertSentTo(
            $admin,
            ReportNotification::class,
            function ($notification, $channels) use ($fileName) {
                return in_array('mail', $channels) && $notification->filePath === $fileName;
            }
        );
    }
}
