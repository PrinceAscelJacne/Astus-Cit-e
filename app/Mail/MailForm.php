<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MailForm extends Mailable
{
    use Queueable, SerializesModels;
    public $details;

    /************Code**********/
    public function __construct($details)
    {
        $this->details = $details;
    }

    public function build()
    {
        return $this->subject('Nouvelle soumission de formulaire de contact')->view('contactform');
    }
    /************EndCode**********/


}
