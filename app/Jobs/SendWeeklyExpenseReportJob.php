<?php

namespace App\Jobs;

use App\Enums\RoleEnum;
use App\Models\User;
use App\Models\Expense;
use App\Mail\WeeklyExpenseReportMail;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
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
        $from = Carbon::now()->startOfWeek();
        $to = Carbon::now()->endOfWeek();


        User::with(['companyExpenses' => function ($query) use ($from, $to) {
            $query->whereBetween('expenses.created_at', [$from, $to]);
        }])
            ->where('role', RoleEnum::ADMIN())
            ->whereHas('companyExpenses', function ($query) use ($from, $to) {
                $query->whereBetween('expenses.created_at', [$from, $to]);
            })
            ->chunk(100, function ($admins) {
                foreach ($admins as $admin) {
                    if ($admin->companyExpenses->isNotEmpty()) {
                        Mail::to($admin->email)->queue(
                            new WeeklyExpenseReportMail($admin, $admin->companyExpenses)
                        );
                    }
                }
            });
    }
}
