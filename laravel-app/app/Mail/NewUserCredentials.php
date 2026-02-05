<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewUserCredentials extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $rawPassword;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $rawPassword)
    {
        $this->user = $user;
        $this->rawPassword = $rawPassword;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Your Account Credentials')
                    ->view('emails.new-user-credentials')
                    ->with([
                        'user' => $this->user,
                        'password' => $this->rawPassword,
                    ]);
    }
}
