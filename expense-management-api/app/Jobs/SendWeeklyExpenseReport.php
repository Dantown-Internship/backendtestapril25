<?php

namespace App\Jobs;

use App\Models\Company;
use App\Models\Expense;
use App\Notifications\WeeklyExpenseReport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWeeklyExpenseReport implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

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
        Log::info('SendWeeklyExpenseReport job started.');
        Company::with('admins')->chunk(100, function ($companies) {
            foreach ($companies as $company) {

                $expenses = Expense::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->whereIn('user_id', $company->users->pluck('id'))->get();

                if ($expenses->isNotEmpty()) {
                    foreach ($company->admins as $admin) {
                        $admin->notify(new WeeklyExpenseReport($expenses));
                    }
                }

            }
        });

        Log::info("Job finished");
    }
}
