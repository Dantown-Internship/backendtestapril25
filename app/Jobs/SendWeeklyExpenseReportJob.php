<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Expense;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;


class SendWeeklyExpenseReportJob implements ShouldQueue
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
        $admins = User::where('role', User::ROLE_ADMIN)->get();

        foreach ($admins as $admin) {
            $expenses = Expense::where('company_id', $admin->company_id)
                               ->where('created_at', '>=', now()->subWeek())
                               ->get();

            Mail::to($admin->email)->queue(new \App\Mail\WeeklyExpenseReportMail($admin, $expenses));
        }
  
    }
}
