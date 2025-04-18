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

    public $admin;

    public function __construct(User $admin)
    {
        $this->admin = $admin;
    }

    public function handle(): void
    {
        $companyId = $this->admin->company_id;

        $expenses = Expense::where('company_id', $companyId)
            ->whereBetween('created_at', [Carbon::now()->subWeek(), Carbon::now()])
            ->with('user')
            ->get();

        Mail::to($this->admin->email)->send(new WeeklyExpenseReportMail($expenses, $this->admin));
    }
}

