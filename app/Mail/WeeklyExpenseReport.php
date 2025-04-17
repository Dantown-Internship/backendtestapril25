<?php

namespace App\Mail;

use App\Models\Company;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Weekly Expense Report Mail Class
 *
 * This class handles the generation and sending of weekly expense reports to company admins.
 * It includes comprehensive expense data, summaries, and visualizations.
 *
 * @package App\Mail
 */
class WeeklyExpenseReport extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The report data containing expense summaries and details
     *
     * @var array
     */
    public array $reportData;

    /**
     * The company for which the report is generated
     *
     * @var Company
     */
    public Company $company;

    /**
     * The admin user who will receive the report
     *
     * @var User
     */
    public User $admin;

    /**
     * Create a new message instance.
     *
     * @param array $reportData The expense report data including summaries and details
     * @param Company $company The company for which the report is generated
     * @param User $admin The admin user who will receive the report
     */
    public function __construct(
        array $reportData,
        Company $company,
        User $admin
    ) {
        $this->reportData = $reportData;
        $this->company = $company;
        $this->admin = $admin;
    }

    /**
     * Get the message envelope.
     *
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Weekly Expense Report for {$this->company->name}",
        );
    }

    /**
     * Get the message content definition.
     *
     * @return Content
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.weekly-expense-report',
            with: [
                'reportData' => $this->reportData,
                'company' => $this->company,
                'admin' => $this->admin,
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
