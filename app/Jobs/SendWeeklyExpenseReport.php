<?php

namespace App\Jobs;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWeeklyExpenseReport implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $admins = User::where('role', 'Admin')->with(['expenses' => function ($query) {
                $query->where('created_at', '>=', now()->subWeek());
            }])->get();

            foreach ($admins as $admin) {
                $expenses = $admin->expenses;

                Log::info("Weekly report sent to {$admin->email}", [
                    'expense_count' => $expenses->count(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Failed to send weekly report: {$e->getMessage()}");
        }
    }
}
