<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WeeklyExpenseMail extends Mailable
{
    use Queueable, SerializesModels;
    public $reportData;

    /**
     * Create a new message instance.
     */
    public function __construct(array $reportData)
    {
        $this->reportData = $reportData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $startDate = $this->reportData['startDate']->format('M d');
        $endDate = $this->reportData['endDate']->format('M d, Y');

        $femail = env('MAIL_FROM_ADDRESS');
        $fname = env('MAIL_FROM_NAME');

        return new Envelope(
            from: new Address($femail, $fname),
            subject: "Weekly Expense Report: {$startDate} - {$endDate}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.weekly-expenses',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        // TODO: add attachment
        return [];
    }
}
