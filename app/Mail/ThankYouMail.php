<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ThankYouMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct($doctor, $patient)
    {
        $this->doctor = $doctor;
	    $this->patient = $patient;
    }

    public function build()
    {
        return $this->view('email.thankyou')
                    ->subject('Thank You from Our Team')
                    ->with(['doctor' => $this->doctor, 'patient' => $this->patient]);
    }
}
