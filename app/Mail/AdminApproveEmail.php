<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminApproveEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $verificationLink;

    public function __construct($verificationLink)
    {
        $this->verificationLink = $verificationLink;
    }

    public function build()
    {
        return $this->view('emails.approveemail');
    }
}