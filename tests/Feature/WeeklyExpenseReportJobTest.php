<?php

namespace Tests\Feature;

use App\Jobs\WeeklyExpenseReportJob;
use App\Models\Company;
use App\Models\User;
use App\Models\Expense;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Event;
use App\Mail\WeeklyExpenseReport;
use Tests\TestCase;

class WeeklyExpenseReportJobTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_sends_expense_reports_to_company_admins()
    {
        Mail::fake();
        
        // Create a company with admin and expenses
        $company = Company::factory()->create(['name' => 'Test Company']);
        
        // Create an admin user
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'company_id' => $company->id,
            'role' => 'Admin'
        ]);
        
        // Create a regular employee
        $employee = User::factory()->create([
            'company_id' => $company->id,
            'role' => 'Employee'
        ]);
        
        // Disable model events to prevent observers from firing
        Event::fake();
        
        // Create some expenses in the last week
        Expense::factory()->count(5)->create([
            'company_id' => $company->id,
            'user_id' => $employee->id,
            'created_at' => now()->subDays(3)
        ]);
        
        // Run the job
        $job = new WeeklyExpenseReportJob();
        $job->handle();
        
        // Assert that the email was sent only to the admin
        Mail::assertSent(WeeklyExpenseReport::class, function ($mail) use ($admin, $company) {
            return $mail->hasTo($admin->email) &&
                   $mail->company->id === $company->id &&
                   $mail->admin->id === $admin->id;
        });
        
        Mail::assertSent(WeeklyExpenseReport::class, 1);
    }
    
    /** @test */
    public function it_does_not_send_reports_for_companies_with_no_expenses()
    {
        Mail::fake();
        
        // Create a company with admin but no expenses
        $company = Company::factory()->create();
        
        $admin = User::factory()->create([
            'company_id' => $company->id,
            'role' => 'Admin'
        ]);
        
        // Run the job
        $job = new WeeklyExpenseReportJob();
        $job->handle();
        
        // Assert that no email was sent
        Mail::assertNothingSent();
    }
    
    /** @test */
    public function it_does_not_send_reports_to_non_admin_users()
    {
        Mail::fake();
        
        // Create a company with a manager and employee, but no admin
        $company = Company::factory()->create();
        
        $manager = User::factory()->create([
            'company_id' => $company->id,
            'role' => 'Manager'
        ]);
        
        $employee = User::factory()->create([
            'company_id' => $company->id,
            'role' => 'Employee'
        ]);
        
        // Disable model events to prevent observers from firing
        Event::fake();
        
        // Create some expenses
        Expense::factory()->count(3)->create([
            'company_id' => $company->id,
            'user_id' => $employee->id,
            'created_at' => now()->subDays(2)
        ]);
        
        // Run the job
        $job = new WeeklyExpenseReportJob();
        $job->handle();
        
        // Assert that no email was sent
        Mail::assertNothingSent();
    }
    
    /** @test */
    public function it_processes_multiple_companies_independently()
    {
        Mail::fake();
        
        // Create two companies
        $company1 = Company::factory()->create(['name' => 'Company 1']);
        $company2 = Company::factory()->create(['name' => 'Company 2']);
        
        // Create admins for each company
        $admin1 = User::factory()->create([
            'company_id' => $company1->id,
            'role' => 'Admin'
        ]);
        
        $admin2 = User::factory()->create([
            'company_id' => $company2->id,
            'role' => 'Admin'
        ]);
        
        // Disable model events to prevent observers from firing
        Event::fake();
        
        // Create expenses for first company within the last week
        Expense::factory()->count(4)->create([
            'company_id' => $company1->id,
            'user_id' => User::factory()->create(['company_id' => $company1->id])->id,
            'created_at' => now()->subDays(1)
        ]);
        
        // Create expenses for second company but older than one week (shouldn't be included in report)
        Expense::factory()->count(3)->create([
            'company_id' => $company2->id,
            'user_id' => User::factory()->create(['company_id' => $company2->id])->id,
            'created_at' => now()->subDays(10) // Older than a week
        ]);
        
        // Run the job
        $job = new WeeklyExpenseReportJob();
        $job->handle();
        
        // Assert email sent only to admin of company with recent expenses
        Mail::assertSent(WeeklyExpenseReport::class, function ($mail) use ($admin1) {
            return $mail->hasTo($admin1->email);
        });
        
        // Assert email NOT sent to admin of company with only old expenses
        Mail::assertNotSent(WeeklyExpenseReport::class, function ($mail) use ($admin2) {
            return $mail->hasTo($admin2->email);
        });
        
        // Assert only one email was sent total
        Mail::assertSent(WeeklyExpenseReport::class, 1);
    }
} 