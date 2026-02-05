<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\TwilioSmsMessage;

class ApplicationApproved extends Notification implements ShouldQueue
{
    use Queueable;

    protected $fullName;

    public function __construct($fullName)
    {
        $this->fullName = $fullName;
    }

    public function via($notifiable)
    {
        return ['mail', 'twilio'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your Solo Parent Application is Approved!')
            ->greeting("Hello {$this->fullName},")
            ->line('Your Solo Parent application has been approved.')
            ->action('View Details', url('/dashboard'))
            ->line('Thank you for using SoloCare!');
    }

    public function toTwilio($notifiable)
    {
       // return (new TwilioSmsMessage())
           // ->content("Hi {$this->fullName}, your Solo Parent application has been approved! ✅");
    }

    public function toArray($notifiable)
    {
        return [
            'full_name' => $this->fullName,
            'status' => 'approved',
        ];
    }
}
