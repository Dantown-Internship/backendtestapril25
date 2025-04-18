<?php

namespace Tests\Feature\Jobs;

use App\Enums\RoleEnum;
use App\Jobs\SendWeeklyExpenseReportJob;
use App\Mail\WeeklyExpenseReportMail;
use App\Models\Company;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendWeeklyExpenseReportJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_admins_with_company_expenses_receive_weekly_report()
    {
        Mail::fake();

        Carbon::setTestNow(Carbon::now()->startOfWeek()->addDays(2));

        $company = Company::factory()->create();

        $admin = User::factory()->for($company)->create([
            'role' => RoleEnum::ADMIN(),
        ]);

        $employee = User::factory()->for($company)->create();

        $expenseInRange = Expense::factory()->for($employee, 'owner')->for($company)->create([
            'created_at' => now()->startOfWeek()->addDay(),
        ]);

        $expenseOutOfRange = Expense::factory()->for($employee, 'owner')->for($company)->create([
            'created_at' => now()->subWeeks(2),
        ]);

        (new SendWeeklyExpenseReportJob)->handle();

        Mail::assertQueued(WeeklyExpenseReportMail::class, function ($mail) use ($admin, $expenseInRange) {
            return $mail->hasTo($admin->email)
                && $mail->expenses->contains($expenseInRange)
                && !$mail->expenses->contains(fn($e) => $e->is($expenseInRange) && $e->created_at->lt(now()->startOfWeek()));
        });

        Mail::assertNotQueued(WeeklyExpenseReportMail::class, function ($mail) use ($expenseOutOfRange) {
            return $mail->expenses->contains($expenseOutOfRange);
        });
    }

    public function test_admins_without_expenses_do_not_receive_mail()
    {
        Mail::fake();

        $admin = User::factory()->create(['role' => RoleEnum::ADMIN()]);

        (new SendWeeklyExpenseReportJob)->handle();

        Mail::assertNothingQueued();
    }
}
