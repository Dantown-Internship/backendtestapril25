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

class SendWeeklyExpenseReports implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        Company::chunk(100, function ($companies) {
            foreach ($companies as $company) {
                $admins = User::where('company_id', $company->id)
                    ->where('role', 'Admin')
                    ->get();

                $expenses = $company->expenses()
                    ->whereBetween('created_at', [now()->subWeek(), now()])
                    ->get();

                foreach ($admins as $admin) {
                    Mail::to($admin->email)
                        ->send(new \App\Mail\WeeklyExpenseReport($expenses));
                }
            }
        });
    }
}