<?php

namespace App\Notifications;

use App\Facades\ExpenseReportPdfGenerator;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExpenseReport extends Notification implements ShouldQueue
{
    use Queueable;


    /**
     * Create a new notification instance.
     */
    public function __construct(private Carbon $startDate, private Carbon $endDate)
    {
        //
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
        $startDate = $this->startDate->format('d/m/Y');
        $endDate = $this->endDate->format('d/m/Y');
        return (new MailMessage)
            ->subject("{$notifiable->company->name} Weekly Expense Report ({$startDate} - {$endDate})")
            ->greeting("Hello  $notifiable->name")
            ->line("Attached to this mail is the weekly expense report for the week of {$startDate} - {$endDate}.")
            ->line('Thank you for using our application!')
            ->attach(ExpenseReportPdfGenerator::getReportStoragePath($notifiable->company->name));
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
