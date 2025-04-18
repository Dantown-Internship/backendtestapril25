<?php

namespace App\Jobs;

use App\Models\Company;
use App\Models\User;
use App\Services\ExpenseService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\WeeklyExpenseReportMail;

class WeeklyExpenseReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(ExpenseService $expenseService): void
    {   
        Company::chunk(100, function ($companies) use ($expenseService) {
            foreach ($companies as $company) {
                $admins = User::where('company_id', $company->id)
                    ->where('role', 'Admin')
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
    }
}