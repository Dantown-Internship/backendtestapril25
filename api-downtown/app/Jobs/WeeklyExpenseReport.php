<?php

namespace App\Jobs;

use App\Models\Companies;
use App\Models\User;
use App\Models\Expenses;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;

class WeeklyExpenseReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

   
    public function handle()
{
    $companies = Companies::all();

    foreach ($companies as $company) {
        $cacheKey = 'admins_' . $company->id;
        $admins = Cache::remember($cacheKey, 3600, function () use ($company) {
            return User::where('company_id', $company->id)
                ->where('role', 'Admin')
                ->get();
        });

        $expenses = Expenses::where('company_id', $company->id)
            ->whereBetween('created_at', [now()->subWeek(), now()])
            ->with(['user'])
            ->get();

        foreach ($admins as $admin) {
            Mail::to($admin->email)->queue(new \App\Mail\WeeklyExpenseReportMail($company, $expenses));
        }
    }
}
}