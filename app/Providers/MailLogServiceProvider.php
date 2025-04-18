<?php

namespace App\Providers;

use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class MailLogServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Log when an email is about to be sent
        Event::listen(function (MessageSending $event) {
            $this->logEmailActivity($event->message, 'sending');
        });

        // Log when an email has been sent
        Event::listen(function (MessageSent $event) {
            $this->logEmailActivity($event->message, 'sent');
        });
    }

    /**
     * Log email activity to the mail log channel
     */
    protected function logEmailActivity($message, $status): void
    {
        $headers = $message->getHeaders();

        $to = [];
        if ($headers->has('To')) {
            $to = $headers->get('To')->getAddresses();
        }

        $toAddresses = array_map(function ($address) {
            return $address->getAddress();
        }, $to);

        $logData = [
            'status' => $status,
            'from' => $headers->has('From') ? $headers->get('From')->getAddresses()[0]->getAddress() : null,
            'to' => implode(', ', $toAddresses),
            'subject' => $headers->has('Subject') ? $headers->get('Subject')->getBodyAsString() : null,
            'message_id' => $headers->has('Message-ID') ? $headers->get('Message-ID')->getBodyAsString() : null,
        ];

        // Log to the mail channel specifically
        Log::channel('mail')->info("Email {$status}", $logData);
    }
}
