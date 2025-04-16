<?php

namespace App\Jobs;

use App\Mail\ExpenseReportMail;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;


class ExpenseReportJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public User $admin, public $expenses) {

    }
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $data = [
                'adminName' => $this->admin->name,
                'companyName' => $this->admin->company->name ?? 'Your Company',
                'expenses' => $this->expenses,
                'totalAmount' => $this->expenses->sum('amount'),
                'reportPeriod' => now()->subWeek()->format('M d, Y') . ' - ' . now()->format('M d, Y')
            ];

            Mail::to($this->admin->email)->send(new ExpenseReportMail($data));

            Log::info("Expense report sent to admin: {$this->admin->email}");
        } catch (\Exception $e) {
            Log::error("Failed to send expense report to {$this->admin->email}: " . $e->getMessage());
            $this->fail($e);
        }
    }

}
