<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProfessionalEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $full_name;
    public $phone_number;
    public $messageBody;
    public $email_adress;

    public function __construct($full_name, $phone_number, $messageBody,$email_adress)
    {
        $this->full_name = $full_name;
        $this->phone_number = $phone_number;
        $this->messageBody = $messageBody;
        $this->email_adress=$email_adress;
    }

    public function build()
    {
        return $this->subject('Message for Professional')
            ->view('emails.professional')
            ->with([
                'full_name' => $this->full_name,
                'phone_number' => $this->phone_number,
                'messageBody' => $this->messageBody,
                'email_adress'=>$this->email_adress,
            ]);
    }
}
