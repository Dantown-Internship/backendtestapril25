<?php

namespace App\Jobs;

use App\Mail\ExpenseReportMail;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class ExpenseReport implements ShouldQueue
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
        $expenses = Expense::all();
        $admins = User::where('role', 'Admin')->get();
        Mail::to($admins)->send(new ExpenseReportMail($expenses));


    }
}
