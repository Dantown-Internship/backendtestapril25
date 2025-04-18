<?php

namespace App\Jobs;

use App\Mail\WeeklyExpenseReport;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

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
        $admins = User::where('role', User::$Admin)->get();
        foreach($admins as $admin){
            $expenses = Expense::where('company_id', $admin->company_id)
            ->whereDate('created_at', '>=', now()->subWeek()->startOfWeek())
            ->whereDate('created_at', '<=', now()->subWeek()->endOfWeek())
            ->with('users')
            ->get();

            if ($expenses->isNotEmpty()) {
            Mail::to($admin->email)->send(new WeeklyExpenseReport($admin, $expenses));
            }
        }
    }
}
