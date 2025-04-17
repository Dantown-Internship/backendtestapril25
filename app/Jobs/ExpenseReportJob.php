<?php

namespace App\Jobs;

use App\Enums\RoleEnum;
use App\Exports\ExpensesExport;
use App\Models\Company;
use App\Models\Expense;
use App\Models\User;
use App\Notifications\ReportNotification;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Maatwebsite\Excel\Facades\Excel;

class ExpenseReportJob implements ShouldQueue
{
    use Queueable;

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
        $companies = Company::query()->select('id', 'name')->get();

        foreach($companies as $company) 
        {
            $lastWeek = Carbon::now()->subDays(7);
            $expenses = Expense::with(['company', 'user'])
            ->where('created_at', '>=', $lastWeek)
            ->where('company_id', $company->id)
            ->get();

            $allCompanyAdmins = User::query()
                ->where('role', RoleEnum::ADMIN->value)
                ->where('company_id', $company->id)
                ->get();

            if($expenses->count() === 0 || $allCompanyAdmins->count() === 0) {
                continue;
            }
            
            $filePath = sprintf('reports/%s_weekly_expenses.xlsx', str_replace(' ', '', $company->name));
            Excel::store(new ExpensesExport($expenses), $filePath, 'public');

            /** @var User $admin */
            foreach($allCompanyAdmins as $admin) 
            {
                $admin->notify(new ReportNotification($filePath, $lastWeek));
            }
        }
    }
}
