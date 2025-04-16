<?php

namespace Tests\Feature;

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
use Tests\TestCase;

class WeeklyExpenseReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_weekly_expense_report_job_generates_pdf_and_sends_email()
    {
        // Mock the Mail facade
        Mail::fake();

        // Mock the Storage facade
        Storage::fake('local');

        // Create a company
        $company = Company::factory()->create();

        // Create an admin user
        $admin = User::factory()->create([
            'company_id' => $company->id,
            'role' => Roles::ADMIN->value,
        ]);

        // Create some expenses
        $expenses = Expense::factory()->count(3)->create([
            'company_id' => $company->id,
            'user_id' => $admin->id,
        ]);

        // Run the job
        $job = new WeeklyExpenseReportJob();
        $job->handle();

        // Assert that the email was sent
        Mail::assertSent(WeeklyExpenseMail::class, function ($mail) use ($admin) {
            return $mail->hasTo($admin->email);
        });

        // Assert that the PDF was generated
        $files = Storage::files('reports');
        $this->assertCount(1, $files);

        // Clean up
        foreach ($files as $file) {
            Storage::delete($file);
        }
    }

    public function test_pdf_service_generates_pdf()
    {
        // Mock the Storage facade
        Storage::fake('local');

        // Create test data
        $data = [
            'company' => Company::factory()->make(),
            'startDate' => now()->subDays(7),
            'endDate' => now(),
            'expenses' => collect([
                Expense::factory()->make([
                    'amount' => 100,
                    'category' => 'Food',
                ]),
                Expense::factory()->make([
                    'amount' => 200,
                    'category' => 'Travel',
                ]),
            ]),
            'totalAmount' => 300,
            'categoryTotals' => [
                'Food' => ['count' => 1, 'total' => 100],
                'Travel' => ['count' => 1, 'total' => 200],
            ],
            'userTotals' => [
                ['user' => 'Test User', 'count' => 2, 'total' => 300],
            ],
        ];

        // Generate PDF
        $path = PdfService::generatePdf('pdf.weekly-expense-report', $data, 'test_report');

        // Assert that the PDF was generated
        $this->assertTrue(Storage::exists($path));

        // Clean up
        Storage::delete($path);
    }
}
