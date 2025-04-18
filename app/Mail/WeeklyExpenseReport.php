<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Collection;

class WeeklyExpenseReport extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The company instance.
     *
     * @var \App\Models\Company
     */
    public $company;

    /**
     * The admin user receiving the report.
     *
     * @var \App\Models\User
     */
    public $admin;

    /**
     * The expenses collection.
     *
     * @var \Illuminate\Support\Collection
     */
    public $expenses;

    /**
     * The expense summary by category.
     *
     * @var array
     */
    public $expenseSummary;

    /**
     * The total expense amount for the period.
     *
     * @var float
     */
    public $totalAmount;

    /**
     * Create a new message instance.
     */
    public function __construct(
        Company $company,
        User $admin,
        Collection $expenses,
        array $expenseSummary,
        float $totalAmount
    ) {
        $this->company = $company;
        $this->admin = $admin;
        $this->expenses = $expenses;
        $this->expenseSummary = $expenseSummary;
        $this->totalAmount = $totalAmount;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Weekly Expense Report for {$this->company->name}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.weekly-expense-report',
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