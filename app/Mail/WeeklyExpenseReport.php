<?php

namespace App\Mail;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WeeklyExpenseReport extends Mailable
{
    use Queueable, SerializesModels;

    public $company;
    public $expenses;
    public $total;

    public function __construct(Company $company, $expenses, $total)
    {
        $this->company = $company;
        $this->expenses = $expenses;
        $this->total = $total;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Weekly Expense Report - ' . $this->company->name)->view('emails.weekly_expense_report');
    }
}
