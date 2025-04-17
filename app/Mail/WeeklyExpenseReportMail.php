<?php

namespace App\Mail;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WeeklyExpenseReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Company $company,
        public $expenses
    ) {
    }

    public function build()
    {
        return $this->subject('Weekly Expense Report')
            ->markdown('emails.weekly-expense-report');
    }
}