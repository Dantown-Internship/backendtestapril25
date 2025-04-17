<?php

namespace App\Jobs;

use App\Mail\WeeklyExpenseReportMail;
use App\Models\Company;
use App\Models\Expense;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendCompanyWeeklyExpenseReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Company $company)
    {
        $this->company = $company;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $admins = $this->company->users()->where('role', 'Admin')->get();

        $expenses = Expense::where('company_id', $this->company->id)
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->get();

        if ($expenses->isEmpty()) {
            return;
        }

        foreach ($admins as $admin) {
            Mail::to($admin->email)->queue((new WeeklyExpenseReportMail($admin, $expenses))->onQueue('report-emails'));
        }
    }
}
