<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPasswordOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;

    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    public function build()
    {
        return $this->subject('SoloCare OTP for Password Reset')
                    ->view('emails.forgot-password-otp')
                    ->with([
                        'otp' => $this->otp,
                    ]);
    }
}
