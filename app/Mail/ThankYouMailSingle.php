<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ThankYouMailSingle extends Mailable
{

     use Queueable, SerializesModels;

    public function __construct($doctor)
    {
        $this->doctor = $doctor;
    }

    public function build()
    {
        return $this->view('email.thankyou')
		  ->subject('Thank You from Our Team')
                    ->with([
                        'doctor' => $this->doctor,
                    ]);
    }
}
