<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $url;
    public $user;

    public function __construct($url, $user)
    {
        $this->url = $url;
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject('Reset Your Password - Multi-Tenant SaaS')
            ->view('emails.reset-password')
            ->with([
                'url' => $this->url,
                'name' => $this->user->name,
            ]);
    }
}
