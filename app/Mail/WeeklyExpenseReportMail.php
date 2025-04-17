<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class WeeklyExpenseReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $admin;
    public $expenses;

    public function __construct($admin, $expenses)
    {
        $this->admin = $admin;
        $this->expenses = $expenses;
    }

    public function build()
    {
        return $this->subject('Weekly Expense Report')
                    ->view('emails.weekly_expense_report');
    }
}

