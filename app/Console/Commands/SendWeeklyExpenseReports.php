<?php

namespace App\Console\Commands;

use App\Jobs\WeeklyExpenseReportJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Send Weekly Expense Reports Command
 *
 * This command dispatches the WeeklyExpenseReportJob to generate and send
 * weekly expense reports to all company admins. It is scheduled to run
 * every Monday at 9:00 AM.
 *
 * @package App\Console\Commands
 */
class SendWeeklyExpenseReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ibidapoexpense:send-weekly-reports';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send weekly expense reports to company admins';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        try {
            $this->info('Starting weekly expense report generation...');

            // Log the start of the process
            Log::info('Weekly expense report generation started', [
                'timestamp' => date('Y-m-d H:i:s'),
                'command' => $this->signature
            ]);

            // Dispatch the job
            WeeklyExpenseReportJob::dispatch();

            $this->info('Weekly expense reports have been queued for processing.');

            // Log successful dispatch
            Log::info('Weekly expense reports queued successfully', [
                'timestamp' => date('Y-m-d H:i:s'),
                'command' => $this->signature
            ]);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Failed to queue weekly expense reports', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'timestamp' => date('Y-m-d H:i:s'),
                'command' => $this->signature
            ]);

            $this->error('Failed to queue weekly expense reports: ' . $e->getMessage());
        }
    }
}
