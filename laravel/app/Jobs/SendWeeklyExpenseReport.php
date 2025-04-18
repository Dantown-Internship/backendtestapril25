<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\WeeklyExpenseReport;

class SendWeeklyExpenseReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @param  mixed  $notifiable
     * @return void
     */
    public function handle()
    {
        $admins = User::where('role', 'Admin')->get();

        foreach ($admins as $admin) {
            $report = $this->generateReport($admin);

            Mail::to($admin->email)->send(new WeeklyExpenseReport($report));
        }
    }

    private function generateReport($admin)
    {
        return $admin->expenses()
            ->whereDate('created_at', '>=', now()->startOfWeek())
            ->whereDate('created_at', '<=', now()->endOfWeek())
            ->get();
    }
}
