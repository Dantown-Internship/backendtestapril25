<?php

namespace App\Jobs;

use App\Models\Company;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendWeeklyExpenseReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public function handle()
    {
        $companies = Company::all();

        foreach ($companies as $company) {
            $admins = $company->users()->where('role', 'Admin')->get();
            $expenses = $company->expenses()->whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ])->get();

            foreach ($admins as $admin) {
                Mail::to($admin->email)->send(new \App\Mail\WeeklyExpenseReport($company, $expenses));
            }
        }
    }
}