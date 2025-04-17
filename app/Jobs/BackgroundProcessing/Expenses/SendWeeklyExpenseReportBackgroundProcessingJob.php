<?php

namespace App\Jobs\BackgroundProcessing\Expenses;

use App\Actions\Company\ListCompaniesAction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendWeeklyExpenseReportBackgroundProcessingJob implements ShouldQueue
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
        $listCompaniesAction = app(ListCompaniesAction::class);

        ['company_payload' => $companies] = $listCompaniesAction->execute([]);

        foreach ($companies as $company) {
            dispatch(
                new SendCompanyWeeklyExpenseReportBackgroundProcessingJob([
                    'company_id' => $company->id
                ])
            );
        }
    }
}
