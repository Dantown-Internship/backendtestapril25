<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Notification;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use App\Notifications\WeeklyExpenseReport;
use App\Models\User;
use App\Models\Scopes\CompanyScope;

class SendReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $admins = User::withoutGlobalScope(CompanyScope::class)->where('role', 'admin')->get();

        $startDate = now()->subWeek()->startOfWeek();

        $endDate = now()->subWeek()->endOfWeek();

        Notification::send(
            notifiables: $admins, 
            notification: new WeeklyExpenseReport(
                    meta: (object)[
                        'startDate' => $startDate,
                        'endDate' => $endDate,
                    ]
                )
        );
        
    }
}
