<?php

namespace App\Jobs;

use App\Models\Company;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendWeeklyExpenseReport implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

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
        Company::with('expenses')->chunk(10, function ($companies) {
            foreach ($companies as $company) {
                $weeklyTotal = $company->expenses()
                    ->whereBetween('created_at', [now()->subWeek(), now()])
                    ->sum('amount');

                Log::info("Company [{$company->name}] spent ₦{$weeklyTotal} this week.");
                
                // Mail::to($company->email)->send(new WeeklyExpenseReport($weeklyTotal));
            }
        });
    }
}
