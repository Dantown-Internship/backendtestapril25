<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WeeklyExpenseReportNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected array $reportData;

    public function __construct(array $reportData)
    {
        $this->reportData = $reportData;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $startDate = $this->reportData['startDate']->format('M d, Y');
        $endDate = $this->reportData['endDate']->format('M d, Y');
        $company = $this->reportData['company'];
        $totalAmount = number_format($this->reportData['totalAmount'], 2);
        $expenseCount = $this->reportData['expenseCount'];
        $categoryStats = $this->reportData['categoryStats'];
        $topExpenses = $this->reportData['topExpenses'];

        return (new MailMessage)
            ->subject("Weekly Expense Report: {$startDate} - {$endDate}")
            ->greeting("Hello {$notifiable->name},")
            ->line("Here's the weekly expense report for {$company->name} for the period {$startDate} to {$endDate}.")
            ->line("**Summary:**")
            ->line("- Total Expenses: \${$totalAmount}")
            ->line("- Number of Expenses: {$expenseCount}")
            ->line("**Expense Categories:**")
            ->line($this->formatCategoryStats($categoryStats))
            ->line("**Top Expenses:**")
            ->line($this->formatTopExpenses($topExpenses))
            ->line('You can view detailed reports in the expense management system.')
            ->action('View Dashboard', url('/'))
            ->line('Thank you for using our application!');
    }


    private function formatCategoryStats($categoryStats): string
    {
        $result = '';
        foreach ($categoryStats as $category => $stats) {
            $total = number_format($stats['total'], 2);
            $result .= "- {$category}: \${$total} ({$stats['count']} expenses)\n";
        }
        return $result;
    }

    private function formatTopExpenses($topExpenses): string
    {
        $result = '';
        foreach ($topExpenses as $index => $expense) {
            $amount = number_format($expense->amount, 2);
            $result .= ($index + 1) . ". {$expense->title}: \${$amount} ({$expense->category})\n";
        }
        return $result;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'company_id' => $this->reportData['company']->id,
            'total_amount' => $this->reportData['totalAmount'],
            'expense_count' => $this->reportData['expenseCount'],
            'start_date' => $this->reportData['startDate'],
            'end_date' => $this->reportData['endDate'],
        ];
    }
}
