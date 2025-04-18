<?php

namespace App\Jobs;

use App\Models\Company;
use App\Services\ExpenseService;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendWeeklyExpenseReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     */
    public function handle(ExpenseService $expenseService, UserService $userService): void
    {
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subWeek();
        
        $companies = Company::all();
        
        foreach ($companies as $company) {
            $expenses = $expenseService->getExpensesForReporting(
                $company->id,
                $startDate->toDateTimeString(),
                $endDate->toDateTimeString()
            );
            
            if ($expenses->isEmpty()) {
                continue;
            }
            
            $totalAmount = $expenses->sum('amount');
            
            $reportData = [
                'company' => $company,
                'startDate' => $startDate->format('Y-m-d'),
                'endDate' => $endDate->format('Y-m-d'),
                'expenses' => $expenses,
                'totalAmount' => $totalAmount,
                'expenseCount' => $expenses->count(),
            ];
            
            $adminUsers = $userService->getCompanyAdmins($company->id);
            
            if ($adminUsers->isEmpty()) {
                continue;
            }
            
            foreach ($adminUsers as $adminUser) {
                Mail::send(
                    'emails.expense-report',
                    $reportData,
                    function ($message) use ($adminUser, $startDate, $endDate) {
                        $message->to($adminUser->email, $adminUser->name)
                            ->subject('Weekly Expense Report: ' . $startDate->format('Y-m-d') . ' to ' . $endDate->format('Y-m-d'));
                    }
                );
            }
        }
    }
}
