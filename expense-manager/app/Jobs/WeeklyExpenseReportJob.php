<?php

namespace App\Jobs;

use App\Enums\Roles;
use App\Mail\WeeklyExpenseMail;
use App\Models\Company;
use App\Models\Expense;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class WeeklyExpenseReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        $companies = User::where('role', Roles::ADMIN->value)
            ->select('company_id')
            ->distinct()
            ->get()
            ->pluck('company_id');

        foreach ($companies as $companyId) {
            $this->generateReportForCompany($companyId);
        }
    }

    /**
     * Generate and send weekly expense report for a company
     *
     * @param int $companyId
     * @return void
     */
    protected function generateReportForCompany(string $companyId): void
    {
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays(7);

        // Get company information
        $company = Company::findOrFail($companyId);

        // Get expenses for the past week
        $expenses = Expense::where('company_id', $companyId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('user')
            ->get();

        // Calculate statistics
        $totalAmount = $expenses->sum('amount');
        $categoryTotals = $expenses->groupBy('category')
            ->map(function ($items) {
                return [
                    'count' => $items->count(),
                    'total' => $items->sum('amount')
                ];
            });

        $userTotals = $expenses->groupBy('user_id')
            ->map(function ($items) {
                return [
                    'user' => $items->first()->user->name ?? "Ghost User",
                    'count' => $items->count(),
                    'total' => $items->sum('amount')
                ];
            });

        // Get all admins for this company
        $admins = User::where('company_id', $companyId)->where('role', Roles::ADMIN->value)->get();

        // Send report email to each admin
        foreach ($admins as $admin) {
            $reportData = [
                'admin' => $admin,
                'company' => $company,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'expenses' => $expenses,
                'totalAmount' => $totalAmount,
                'categoryTotals' => $categoryTotals,
                'userTotals' => $userTotals,
            ];

            Mail::to($admin->email)->send(new WeeklyExpenseMail($reportData));
        }
    }
}
