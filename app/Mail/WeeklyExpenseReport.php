<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WeeklyExpenseReport extends Mailable
{
    use Queueable, SerializesModels;

    public $company;
    public $expenses;

    public function __construct($company, $expenses)
    {
        $this->company = $company;
        $this->expenses = $expenses;
    }

    public function build()
    {
        return $this->subject('Weekly Expense Report')
                    ->view('emails.weekly_expense_report');
    }
}