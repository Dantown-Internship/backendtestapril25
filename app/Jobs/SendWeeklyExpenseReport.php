<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\WeeklyExpenseReport;
use App\Models\User;
use App\Models\Expense;
use Illuminate\Support\Facades\Mail;

class SendWeeklyExpenseReport implements ShouldQueue
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
    public function handle()
    {
        // Get all expenses from the last week grouped by company_id
        $expensesByCompany = Expense::with('user')
            ->where('created_at', '>=', now()->subWeek())
            ->get()
            ->groupBy('company_id');

        foreach ($expensesByCompany as $companyId => $expenses) {
            // Get Admins of this company
            $admins = User::where('company_id', $companyId)
                          ->where('role', 'Admin')
                          ->get();

                          $companyName = optional($expenses->first()->user->company)->name ?? 'Your Company';
            foreach ($admins as $admin) {
                
                Mail::to($admin->email)->queue(new WeeklyExpenseReport($expenses,$companyName));
            }
        }
    }
}
