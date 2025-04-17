<?php

namespace App\Jobs;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendWeeklyExpenseReport implements ShouldQueue
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
        $companies = Company::all();

        foreach ($companies as $company) {
            $admins = $company->users()->where('role', 'Admin')->get();

            if ($admins->isEmpty()) continue;

            $startDate = now()->subWeek()->format('Y-m-d');
            $endDate = now()->format('Y-m-d');

            $expenses = $company->expenses()
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            $total = $expenses->sum('amount');
            $formattedTotal = number_format($total, 2);

            foreach ($admins as $admin) {
                Mail::raw(
                    "Your weekly expense report from $startDate to $endDate is ready. Total expenses: $formattedTotal.",
                    function ($message) use ($admin, $formattedTotal, $startDate, $endDate) {
                        $message->to($admin->email)
                            ->subject("Weekly Report [$startDate → $endDate] | Total: $formattedTotal");
                    }
                );
            }
        }
    }
}
