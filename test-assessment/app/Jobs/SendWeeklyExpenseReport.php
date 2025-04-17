<?php

namespace App\Jobs;

use App\Enum\UserRole;
use App\Mail\ExpenseReportMail;
use App\Mail\ReportMail;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendWeeklyExpenseReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // protected $totalExpense;
    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        // $this->user = $totalExpense;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $admins = User::where('role', UserRole::Admin)->get();

        foreach ($admins as $admin) {
            $totalExpense = Expense::where('company_id', $admin->company_id)->sum('amount');
            Mail::to($admin->email)->queue(new ReportMail($totalExpense));
        }
        
    }
}
