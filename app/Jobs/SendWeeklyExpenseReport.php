<?php

namespace App\Jobs;

use App\Mail\WeeklyExpenseReportMail;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendWeeklyExpenseReport implements ShouldQueue
{
    use Queueable;

    public $admin;
    public $expenses;

    /**
     * Create a new job instance.
     */
    public function __construct($admin, $expenses)
    {
        $this->admin = $admin;
        $this->expenses = $expenses;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        Mail::to($this->admin->email)->queue(new WeeklyExpenseReportMail($this->admin,$this->expenses));
    }
}
