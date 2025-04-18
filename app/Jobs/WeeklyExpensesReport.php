<?php

namespace App\Jobs;

use App\Constants\UserRole;
use App\Mail\ExpensesReport;
use App\Models\Expense;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class WeeklyExpensesReport implements ShouldQueue
{
    use Queueable;

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
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $admins = User::where('role', UserRole::ADMIN)->get();
        $expenses = Expense::with(['company', 'user'])->whereBetween('created_at', [$startOfWeek, $endOfWeek])->get();

        $admins->each(fn($admin) => Mail::to($admin)->send(new ExpensesReport($admin, $expenses)));
    }
}
