<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\User;
use App\Mail\ExpenseReportMail;
use Illuminate\Support\Facades\Mail;

class SendExpenseReport implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(){}

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $admins = User::select('id','name', 'email', 'status')->whereHas('role', function ($query) {
            $query->where('name', 'admin');
        })->get();
    
        foreach ($admins as $admin) {
            Mail::to($admin->email)->queue(new ExpenseReportMail($admin));
        }
    }
}
