<?php

namespace App\Jobs;

use App\Enums\UserRole;
use App\Mail\WeeklyExpenseReport;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendWeeklyExpenseReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $companies = Company::all();

        foreach ($companies as $company) {
            $admins = $company->users()->where('role', UserRole::Admin->value)->get();

            $lastWeek = Carbon::now()->subWeek();
            $expenses = $company->expenses()
                ->where('created_at', '>=', $lastWeek)
                ->with('user')
                ->get();

            if ($expenses->count() > 0 && $admins->count() > 0) {
                foreach ($admins as $admin) {
                    Mail::to($admin)->send(new WeeklyExpenseReport($company, $expenses));
                }
            }
        }
    }
}
