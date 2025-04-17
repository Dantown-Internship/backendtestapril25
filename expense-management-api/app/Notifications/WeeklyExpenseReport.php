<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WeeklyExpenseReport extends Notification
{
    use Queueable;

    /**
     * The expenses collection.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $expenses;

    /**
     * Create a new notification instance.
     *
     * @param \Illuminate\Support\Collection $expenses
     */
    public function __construct($expenses)
    {
        $this->expenses = $expenses;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $company = $notifiable->company;
        $startDate = now()->startOfWeek()->format('Y-m-d');
        $endDate = now()->endOfWeek()->format('Y-m-d');
        $totalAmount = $this->expenses->sum('amount');
        $formattedTotalAmount = number_format($totalAmount, 2);

        return (new MailMessage)
            ->subject("Weekly Expense Report for  {$company->name} for the week of {$startDate} to {$endDate}")
            ->greeting("Hello {$notifiable->name},")
            ->line("Here is the weekly expense report for {$company->name} for the week of {$startDate} to {$endDate}.")
            ->line('Total Expenses: $' . $formattedTotalAmount)
            ->line('Expenses:')
            ->line('-----------------------------------')
            ->line('Below is a summary of your expenses:')
            ->line('<table border="1" style="border-collapse: collapse; width: 100%;">')
            ->line('<thead>')
            ->line('<tr>')
            ->line('<th style="padding: 8px; text-align: left;">Title</th>')
            ->line('<th style="padding: 8px; text-align: left;">Category</th>')
            ->line('<th style="padding: 8px; text-align: left;">Amount</th>')
            ->line('</tr>')
            ->line('</thead>')
            ->line('<tbody>')
            ->line($this->expenses->map(function ($expense) {
                return "<tr>
                            <td style='padding: 8px;'>{$expense->title}</td>
                            <td style='padding: 8px;'>{$expense->category}</td>
                            <td style='padding: 8px;'>$" . number_format($expense->amount, 2) . "</td>
                        </tr>";
            })->implode(''))
            ->line('</tbody>')
            ->line('</table>')
            ->action('View Expenses', url('/expenses'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
