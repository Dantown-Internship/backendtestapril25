<?php

namespace App\Jobs\BackgroundProcessing\Expenses;

use App\Actions\AuditLog\CreateAuditLogAction;
use App\Actions\Company\GetCompanyByIdAction;
use App\Actions\Company\ListCompaniesAction;
use App\Actions\Expense\GetWeeklyExpensesAction;
use App\Actions\User\GetUserByIdAction;
use App\Actions\User\ListUsersAction;
use App\Mail\Reports\SendWeeklyExpenseReportMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendCompanyWeeklyExpenseReportBackgroundProcessingJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private array $sendCompanyWeeklyExpenseReportBackgroundProcessingJobOptions)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $getCompanyByIdAction = app(GetCompanyByIdAction::class);

        $companyId = $this->sendCompanyWeeklyExpenseReportBackgroundProcessingJobOptions['company_id'];

        $company = $getCompanyByIdAction->execute($companyId);

        if (is_null($company)) {
            return;
        }

        $GetWeeklyExpensesAction = app(GetWeeklyExpensesAction::class);

        $relationships = ['company', 'user'];

        $weeklyExpenses = $GetWeeklyExpensesAction->execute([
            'company_id' => $companyId
        ], $relationships);

        $listUsersAction = app(ListUsersAction::class);

        ['user_payload' => $users] = $listUsersAction->execute([
            'filter_record_options_payload' => [
                'company_id' => $companyId,
                'role' => 'Admin'
            ]
        ]);

        foreach ($users as $user) {
            Mail::to($user->email)->sendNow(
                new SendWeeklyExpenseReportMail(
                    $company,
                    $user,
                    $weeklyExpenses
                )
            );
        }
    }
}
