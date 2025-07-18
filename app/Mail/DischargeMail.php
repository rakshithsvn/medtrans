<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class DischargeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct($doctor, $patient, $summaryFilePath)
    {
        $this->doctor = $doctor;
	$this->patient = $patient;
        $this->summaryFilePath = $summaryFilePath;
    }

    public function build()
    {
        $email = $this->view('email.thankyou')
        ->subject('Discharge Summary')
        ->with([
            'doctor' => $this->doctor,
            'patient' => $this->patient,
        ]);

	$fullPath = storage_path('app/public/' . $this->summaryFilePath);
        if ($this->summaryFilePath && Storage::disk('public')->exists($this->summaryFilePath)) {
            $email->attach($fullPath, [
                'as' => 'discharge_summary.pdf',
                'mime' => 'application/pdf',
            ]);
        }
        return $email;
    }
}
