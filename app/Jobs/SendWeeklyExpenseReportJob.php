<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Expense;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Mail\WeeklyExpenseReportMail;
use Illuminate\Support\Facades\Mail;

class SendWeeklyExpenseReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(){ }

    public function handle()
    {
        // Get all admins
        $admins = User::where('role', 'Admin')->get();

        foreach ($admins as $admin) {
            // Fetch expenses for the admin's company
            $expenses = $admin->company->expenses()
                ->with('user')
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->latest()
                ->get();

            $totalAmount = $expenses->sum('amount');

            Mail::to($admin->email)->queue(new WeeklyExpenseReportMail($admin, $expenses, $totalAmount));
        }
    }
}

