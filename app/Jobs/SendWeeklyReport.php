<?php

namespace App\Jobs;

use App\Mail\WeeklyReportMail;
use App\Models\Expenses;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendWeeklyReport implements ShouldQueue
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
        // get admins
        $admins = User::where('role', 'Admin')->get();

        foreach($admins as $admin){
            $companyId = $admin->company_id;
            
            $total = Expenses::where('company_id', $companyId)
            ->where('created_at', '>=', now()->subWeek())->count();

            $totalAmount = Expenses::where('company_id', $companyId)
            ->where('created_at', '>=', now()->subWeek())->sum('amount');

            Mail::to($admin->email)->send(new WeeklyReportMail($total, $totalAmount));
        }   
    }
}
