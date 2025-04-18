<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Expense;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendWeeklyExpenseReportJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $admins = User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
            $expenses = Expense::where('company_id', $admin->company_id)
                ->whereBetween('date', [Carbon::now()->subWeek(), Carbon::now()])
                ->get();

            $total = $expenses->sum('amount');

            Log::info("Weekly Expense Report for Admin: {$admin->name}", [
                'company_id' => $admin->company_id,
                'total_expense' => $total,
                'expenses_count' => $expenses->count(),
            ]);

            // You can replace the Log with email logic later
        }
    }
}