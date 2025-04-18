<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;

class WeeklyExpenseReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $admin;
    public $expenses;

    public function __construct(User $admin, $expenses)
    {
        $this->admin = $admin;
        $this->expenses = $expenses;
    }

    public function build()
    {
        return $this->subject('Weekly Expense Report')
            ->markdown('emails.expenses.weekly');
    }
}
