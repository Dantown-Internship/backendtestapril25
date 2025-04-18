<?php

namespace App\Jobs;

use App\Mail\ExpenseReportMail;
use App\Services\UserService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendWeeklyExpenseReport implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct() {}


    /**
     * Execute the job.
     */
    public function handle(UserService $userService): void
    {
        $admins = $userService->getAdmins();

        foreach ($admins as $admin) {
            $expenses = $admin->company->expenses()
                ->where('created_at', '>=', now()->subWeek())
                ->get();

            $data = [
                'admin' => $admin->name,
                'company' => $admin->company->name,
                'expenses' => $expenses,
                'total' => $expenses->sum('amount'),
            ];

            Mail::to($admin->email)->send(new ExpenseReportMail($data));
        }
    }
}
