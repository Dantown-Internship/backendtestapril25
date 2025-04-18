<?php

namespace App\Mail;

use App\Models\Company;
use App\Models\Expense;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WeeklyReportEmail extends Mailable
{
    use Queueable, SerializesModels;

    public Company $company;
    public array | Collection $expenses;

    /**
     * Create a new message instance.
     */
    public function __construct(Company $company, Collection | Expense ...$expenses)
    {
        $this->company = $company;
        $this->expenses = $expenses;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Weekly Expense Report for ' . $this->company->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.weekly-report', // Create this Blade template
            html: 'emails.weekly-report-html', // Optional HTML version
            with: [
                'companyName' => $this->company->name,
                'expenses' => $this->expenses,
            ],
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