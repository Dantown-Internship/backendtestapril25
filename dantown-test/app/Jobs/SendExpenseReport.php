<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Expense;
use Illuminate\Support\Facades\Mail;
use App\Mail\WeeklyExpenseReport;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendExpenseReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
     
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        try {
           
            $startDate = Carbon::now()->subWeek()->startOfWeek();
            $endDate = Carbon::now()->subWeek()->endOfWeek();

            $admins = User::where('role', 'admin')->get();

            foreach ($admins as $admin) {
                $companyId = $admin->company_id;
                $expenses = Expense::where('company_id', $companyId)
                    ->whereBetween('expense_date', [$startDate, $endDate])
                    ->get();

                $reportData = [
                    'adminName' => $admin->name,
                    'startDate' => $startDate->format('Y-m-d'),
                    'endDate' => $endDate->format('Y-m-d'),
                    'expenses' => $expenses,
                    'totalExpenses' => $expenses->sum('amount'),
                ];

                Mail::to($admin->email)->send(new WeeklyExpenseReport($reportData));

                Log::info('Weekly expense report sent to: ' . $admin->email);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send weekly expense reports: ' . $e->getMessage());
            // retry the job
            $this->release(60);
        }
    }
}

