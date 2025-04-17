<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Expense;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendWeeklyExpenseReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $admins = User::where('role', 'Admin')->get();

        foreach ($admins as $admin) {
            $expenses = Expense::where('company_id', $admin->company_id)
                ->where('created_at', '>=', now()->subWeek())
                ->get();

            if ($expenses->isNotEmpty()) {
                Mail::to($admin->email)->send(new \App\Mail\WeeklyExpenseReportMail($admin, $expenses));
            }
        }
    }
}

