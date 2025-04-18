<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Company;
use App\Models\User;
use App\Models\Expense;
use App\Mail\WeeklyExpenseReport;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class WeeklyExpenseReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Starting WeeklyExpenseReportJob...');
        
        // Get all companies
        $companies = Company::all();
        Log::info('Found ' . $companies->count() . ' companies to process');
        
        foreach ($companies as $company) {
            try {
                $this->processCompany($company);
            } catch (\Exception $e) {
                Log::error('Error processing company #' . $company->id . ': ' . $e->getMessage());
                // Continue with other companies even if one fails
            }
        }
        
        Log::info('WeeklyExpenseReportJob completed successfully');
    }
    
    /**
     * Process a single company's weekly expense report
     */
    private function processCompany(Company $company): void
    {
        Log::info('Processing weekly report for company: ' . $company->name . ' (ID: ' . $company->id . ')');
        
        // Get admin users for the company
        $admins = User::where('company_id', $company->id)
            ->where('role', 'Admin')
            ->get();
        
        // Skip if there are no admins
        if ($admins->isEmpty()) {
            Log::info('No admin users found for company #' . $company->id . ', skipping');
            return;
        }
        
        Log::info('Found ' . $admins->count() . ' admin users for company #' . $company->id);
        
        // Get weekly expenses
        $expenses = Expense::where('company_id', $company->id)
            ->whereBetween('created_at', [now()->subWeek(), now()])
            ->with('user') // Eager load user relation
            ->get();
        
        Log::info('Found ' . $expenses->count() . ' expenses for company #' . $company->id . ' in the last week');
        
        // Skip sending empty reports if no expenses
        if ($expenses->isEmpty()) {
            Log::info('No expenses found for company #' . $company->id . ' in the last week, skipping report');
            return;
        }
        
        // Group expenses by category for summary
        $expensesByCategory = $expenses->groupBy('category');
        $expenseSummary = [];
        
        foreach ($expensesByCategory as $category => $categoryExpenses) {
            $expenseSummary[$category] = [
                'count' => $categoryExpenses->count(),
                'total' => $categoryExpenses->sum('amount'),
            ];
        }
        
        // Calculate company-wide total
        $totalAmount = $expenses->sum('amount');
        
        // Send report to each admin
        foreach ($admins as $admin) {
            try {
                Log::info('Sending expense report to admin: ' . $admin->name . ' (' . $admin->email . ')');
                
                Mail::to($admin)->send(new WeeklyExpenseReport(
                    $company,
                    $admin,
                    $expenses,
                    $expenseSummary,
                    $totalAmount
                ));
                
                Log::info('Report sent successfully to ' . $admin->email);
            } catch (\Exception $e) {
                Log::error('Failed to send report to ' . $admin->email . ': ' . $e->getMessage());
                // Continue with other admins even if one fails
            }
        }
    }
} 