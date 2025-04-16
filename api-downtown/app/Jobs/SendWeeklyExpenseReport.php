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

class SendWeeklyExpenseReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $companies = Companies::all();

        foreach ($companies as $company) {
            $admins = User::where('company_id', $company->id)
                ->where('role', 'Admin')
                ->get();

            $expenses = Expenses::where('company_id', $company->id)
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->get();

            foreach ($admins as $admin) {
                Mail::to($admin->email)->send(new \App\Mail\WeeklyExpenseReportMail($company, $expenses));
            }
        }
    }
}