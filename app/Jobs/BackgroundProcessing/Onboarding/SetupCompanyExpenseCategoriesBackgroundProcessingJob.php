<?php

namespace App\Jobs\BackgroundProcessing\Onboarding;

use App\Actions\Company\GetCompanyByIdAction;
use App\Actions\ExpenseCategory\CreateExpenseCategoryAction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class SetupCompanyExpenseCategoriesBackgroundProcessingJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private array $setupCompanyExpenseCategoriesBackgroundProcessingJobOptions)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $companyId = $this->setupCompanyExpenseCategoriesBackgroundProcessingJobOptions['company_id'];

        $getCompanyByIdAction = app(GetCompanyByIdAction::class);

        $company = $getCompanyByIdAction->execute($companyId);

        if (is_null($company)) {
            return;
        }

        DB::transaction(function () use ($company) {
            $createExpenseCategoryAction = app(CreateExpenseCategoryAction::class);

            $expenseCategories = [
                'Wages & Salaries',
                'Office Utilities',
                'Transportation',
                'Gifts'
            ];

            foreach ($expenseCategories as $expenseCategory) {
                $createExpenseCategoryAction->execute([
                    'company_id' => $company->id,
                    'name' => $expenseCategory
                ]);
            }
        });
    }
}
