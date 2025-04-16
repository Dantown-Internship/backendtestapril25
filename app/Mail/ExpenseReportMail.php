<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;

class ExpenseReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $expenses;
    public $from;
    public $to;

    /**
     * Create a new message instance.
     */
    public function __construct(Collection $expenses, $from, $to)
    {
        $this->expenses = $expenses;
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject("Weekly Expense Report: {$this->from->format('Y-m-d')} - {$this->to->format('Y-m-d')}")
            ->view('emails.expense_report');
    }
}