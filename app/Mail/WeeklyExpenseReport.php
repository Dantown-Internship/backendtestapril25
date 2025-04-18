<?php

namespace App\Mail;

use App\Models\Company;
use App\Models\Expense;
use App\Models\User;
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
     * Create a new message instance.
     */
    public function __construct(
        public User $admin,
        public Company $company,
        public Collection $expenses
    ) {}

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
        $totalAmount = $this->expenses->sum('amount');
        $expensesCount = $this->expenses->count();
        $categorySummary = $this->expenses
            ->groupBy('category')
            ->map(function ($expenses, $category) {
                return [
                    'category' => $category,
                    'count' => $expenses->count(),
                    'total' => $expenses->sum('amount')
                ];
            });

        return new Content(
            markdown: 'emails.expenses.weekly-report',
            with: [
                'admin' => $this->admin,
                'company' => $this->company,
                'expenses' => $this->expenses,
                'expensesCount' => $expensesCount,
                'totalAmount' => $totalAmount,
                'categorySummary' => $categorySummary,
                'reportDate' => now()->format('F d, Y'),
            ],
        );
    }
}
