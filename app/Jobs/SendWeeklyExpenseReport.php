<?php

namespace App\Jobs;

use App\Models\User;
//use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

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
    public function handle(): void
    {
        // Retrieve all Admin users.
        $admins = User::where('role', 'Admin')->get();

        foreach ($admins as $admin) {
            // Dispatch the email (ensure you have configured your mail settings).
            Mail::to($admin->email)->send(new \App\Mail\WeeklyExpenseReportMail($admin));
        }
    }
}
