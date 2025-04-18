<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Expense;
use App\Models\Company;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Jobs\SendWeeklyExpenseReportJob;
use App\Mail\WeeklyExpenseReportMail;


class SendWeeklyReportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed company and user
        $this->seed(\Database\Seeders\CompanySeeder::class);

    }

    /* public function test_weekly_expense_report_job_dispatches_correctly()
    {
        Queue::fake();

        // Create Admin users and some expenses
        $admin1 = User::factory()->create(['role' =>'Admin', 'company_id' => 1]);
        $admin2 = User::factory()->create(['role' =>'Admin', 'company_id' => 2]);

        Expense::factory()->count(5)->create(['company_id' => 1, 'user_id' => $admin1->id]);
        Expense::factory()->count(3)->create(['company_id' => 2, 'user_id' => $admin2->id]);

        // Simulate scheduled command
        Artisan::call('schedule:run');

        Queue::assertPushed(SendWeeklyExpenseReportJob::class, function ($job) use ($admin1) {
            return $job->admin->id === $admin1->id;
        });

        Queue::assertPushed(SendWeeklyExpenseReportJob::class, function ($job) use ($admin2) {
            return $job->admin->id === $admin2->id;
        });
    } */

    public function test_weekly_expense_report_job_dispatches_correctly()
    {
        Queue::fake();

        SendWeeklyExpenseReportJob::dispatch(); 

        Queue::assertPushed(SendWeeklyExpenseReportJob::class);
    }


    public function test_expense_report_is_sent_to_admin()
    {
        Mail::fake();

        $admin = User::factory()->create(['role' => 'Admin']);
        $company = Company::factory()->create();
        $admin->company()->associate($company)->save();

        $user = User::factory()->create(['company_id' => $company->id]);
        Expense::factory()->count(3)->create([
            'company_id' => $company->id,
            'user_id' => $user->id,
            'created_at' => now()->subDays(2),
        ]);

        SendWeeklyExpenseReportJob::dispatchSync();

        Mail::assertQueued(WeeklyExpenseReportMail::class, function (WeeklyExpenseReportMail $mail) use ($admin) {
            return $mail->hasTo($admin->email);
        });
    }


}
