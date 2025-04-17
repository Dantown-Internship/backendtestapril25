<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class WeeklyExpenseReport extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The expenses collection for the report.
     *
     * @var Collection
     */
    public $expenses;

    /**
     * The total amount of expenses for the week.
     *
     * @var float
     */
    public $totalAmount;

    /**
     * The start date of the report period.
     *
     * @var string
     */
    public $startDate;

    /**
     * The end date of the report period.
     *
     * @var string
     */
    public $endDate;

    /**
     * Create a new message instance.
     *
     * @param Collection $expenses
     */
    public function __construct(Collection $expenses)
    {
        $this->expenses = $expenses;
        $this->totalAmount = $expenses->sum('amount');
        $this->startDate = now()->subWeek()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Weekly Expense Report - ' . $this->startDate . ' to ' . $this->endDate,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'emails.weekly-expense-report',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}