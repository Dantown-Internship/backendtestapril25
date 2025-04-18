<?php
namespace App\Jobs;

use App\Models\User;
use App\Models\Expense;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};
use Illuminate\Support\Facades\Mail;

class SendWeeklyExpenseReport implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $admins = User::where('role', 'Admin')->get();

        foreach ($admins as $admin) {
            $expenses = Expense::where('company_id', $admin->company_id)
                ->where('created_at', '>=', now()->subWeek())
                ->get();

            Mail::to($admin->email)->queue(new \App\Mail\WeeklyExpenseReport($admin, $expenses));
        }
    }
}
