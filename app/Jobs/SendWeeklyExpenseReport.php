<?php

namespace App\Jobs;

use App\Models\Company;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\WeeklyExpenseReport;

class SendWeeklyExpenseReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $companies = Company::all();

        foreach ($companies as $company) {
            $admins = User::where('company_id', $company->id)
                ->where('role', 'Admin')
                ->get();

            if ($admins->isEmpty()) {
                continue;
            }

            $expenses = $company->expenses()
                ->whereBetween('created_at', [now()->subWeek(), now()])
                ->get();

            $total = $expenses->sum('amount');

            foreach ($admins as $admin) {
                Mail::to($admin->email)
                    ->send(new WeeklyExpenseReport($company, $expenses, $total));
            }
        }
    }
}
