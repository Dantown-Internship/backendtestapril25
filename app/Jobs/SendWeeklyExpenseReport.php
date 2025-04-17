<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Expense;
use App\Mail\ExpenseReportMail;
use App\Models\Scopes\CompanyScope;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Carbon\Carbon;

class SendWeeklyExpenseReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle()
    {
        // Get all admins (across all companies)
        $admins = User::withoutGlobalScope(CompanyScope::class)->where('role', User::ADMIN)->get();

        // Get the date range for the previous week
        $from = Carbon::now()->subWeek()->startOfWeek();
        $to = Carbon::now()->subWeek()->endOfWeek();

        // For each admin, fetch expenses for their company and send a report
        foreach ($admins as $admin) {
            // Fetch only expenses for the admin's company in the previous week
            $expenses = Expense::where('company_id', $admin->company_id)
                ->whereBetween('date', [$from, $to])
                ->get();

            // Only send email if there are expenses
            if ($expenses->isNotEmpty()) {
                Mail::to($admin->email)->send(new ExpenseReportMail($expenses, $from->format("d M, Y"), $to->format("d M, Y")));
            }
        }
    }
}