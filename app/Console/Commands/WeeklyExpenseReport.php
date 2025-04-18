<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use Illuminate\Console\Command;
use App\Mail\WeeklyExpenseReportMail;
use App\Models\Company;
use App\Models\User;
use App\Services\ExpenseService;
use Illuminate\Support\Facades\Mail;

class WeeklyExpenseReport extends Command
{
    protected $signature = 'weekly-expense-report:generate';
    protected $description = 'Generate a weekly expense report';

    public function handle(ExpenseService $expenseService)
    {
        Company::chunk(100, function ($companies) use ($expenseService) {
            foreach ($companies as $company) {
                $admins = User::where('company_id', $company->id)
                    ->where('role', UserRole::Admin)
                    ->get();
                
                if ($admins->isEmpty()) {
                    continue;
                }
                
                $expenses = $expenseService->generateReport(
                    $company->id,
                    ['start_date' => now()->subWeek(), 'end_date' => now()]
                );
                foreach ($admins as $admin) {
                    Mail::to($admin->email)
                        ->send(new WeeklyExpenseReportMail($company, $expenses));
                }
            }
        });

        $this->info('Weekly expense report job has been dispatched.');

        return 0;
    }
}