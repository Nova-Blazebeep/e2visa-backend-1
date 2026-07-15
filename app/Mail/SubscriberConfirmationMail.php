<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubscriberConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subscriber;
    public $nameOrEmail;

    public function __construct($subscriber, $nameOrEmail)
    {
        $this->subscriber = $subscriber;
        $this->nameOrEmail = $nameOrEmail;
    }

    public function build()
    {
        return $this->subject('Thank you for subscribing!')
            ->view('emails.subscriber_confirmation');
    }
}
