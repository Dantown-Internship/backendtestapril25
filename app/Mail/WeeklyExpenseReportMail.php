<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class WeeklyExpenseReportMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $admin;
    public $expenses;
    public $total;

    public function __construct($admin, $expenses, $total)
    {
        $this->admin = $admin;
        $this->expenses = $expenses;
        $this->total = $total;
    }

    public function build()
    {
        return $this->subject('Weekly Expense Report')
            ->view('emails.weekly_expense')
            ->with([
                'admin' => $this->admin,
                'expenses' => $this->expenses,
                'total' => $this->total,
            ]);
    }
}
