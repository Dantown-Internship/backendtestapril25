<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WeeklyExpenseReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public $count;

    public $total;

    public $average;

    public $largest;

    public $smallest;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $expenses)
    {
        $this->user = $user;
        $this->count = $expenses->count();
        $this->total = $expenses->sum('amount');
        $this->average = $expenses->avg('amount') ?? 0;
        $this->largest = $expenses->sortByDesc('amount')->first();
        $this->smallest = $expenses->sortBy('amount')->first();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Weekly Expense Report',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.weekly-expense-report',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
