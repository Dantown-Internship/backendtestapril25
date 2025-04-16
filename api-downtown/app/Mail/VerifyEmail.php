<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;



class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $verificationUrl;
    public $user;

    public function __construct($verificationUrl, $user)
    {
        $this->verificationUrl = $verificationUrl;
        $this->user = $user;
    }

    public function build()
    {
        $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');

        return $this->subject('Verify Your Email Address')
                    ->view('emails.verify-email')
                    ->with([
                        'url' => $this->verificationUrl,
                        'login_url' => $frontendUrl . '/login',
                        'user' => $this->user // Ensure this is passed correctly
                    ]);
    }
}
