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
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendWeeklyExpenseReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        try {
            $startDate = Carbon::now()->subWeek()->startOfWeek();
            $endDate = Carbon::now()->subWeek()->endOfWeek();

            $expenses = Expense::with(['user', 'company'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get()
                ->groupBy('company_id');

            $admins = User::where('role', 'Admin')->get();
            Log::info($admins);
            foreach ($admins as $admin) {
                Mail::to($admin->email)->send(new \App\Mail\WeeklyExpenseReport($admin, $expenses));
            }
            Log::info('Weekly expense report job executed');
        } catch (\Throwable $th) {
            Log::error("Weekly report job failed: " . $th->getMessage());
        }
    }
}