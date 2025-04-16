<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Expense;
use App\Mail\ExpenseReportMail;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Carbon\Carbon;

class SendWeeklyExpenseReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle()
    {
        // Get all admins
        $admins = User::where('role', User::ADMIN)->get();
        // Get expenses from the last week
        $from = Carbon::now()->subWeek()->startOfWeek();
        $to = Carbon::now()->subWeek()->endOfWeek();
        $expenses = Expense::whereBetween('date', [$from, $to])->get();

        // Send report to each admin
        foreach ($admins as $admin) {
            Mail::to($admin->email)->send(new ExpenseReportMail($expenses, $from, $to));
        }
    }
}
