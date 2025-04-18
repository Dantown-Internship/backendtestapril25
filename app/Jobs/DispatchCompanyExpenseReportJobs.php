<?php

namespace App\Jobs;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;

class DispatchCompanyExpenseReportJobs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        $batch = Bus::batch([])->name('Weekly Reports to Companies')->dispatch(); // empty start

        Company::chunk(10000, function ($companies) use ($batch) {
            $batchedJobs = collect();
            $companies->each(function ($company) use ($batchedJobs) {
                $batchedJobs->push(new SendCompanyWeeklyReportJob($company));
            });
            $batchedJobs->chunk(1000)->each(function ($chunk) use ($batch) {
                $batch->add($chunk);
            });
        });

    }
}
