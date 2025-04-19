<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\WeeklyExpenseReport;

class SendWeeklyExpenseReport implements ShouldQueue
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
    public function handle()
    {
        $admins = User::where('role', 'Admin')->get();
    
        foreach ($admins as $admin) {
            Mail::to($admin->email)->send(new WeeklyExpenseReport($admin));
        }
    }
}




