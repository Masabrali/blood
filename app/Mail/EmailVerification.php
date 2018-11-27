<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailVerification extends Mailable
{
    use Queueable, SerializesModels;

    /**
    * The user instance.
    *
    * @var User
    */
    public $user;

    /**
    * The verification code.
    *
    * @var Integer
    */
    public $code;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $code)
    {
        $this->user = $user;
        $this->code = $code;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        return $this->from([
            'address'=>'support@firstpride.co.tz', 'name'=>'NBTP TECH SUPPORT'
        ])
        ->view('emails.email_verification')->text('emails.email_verification_plain');

    }
}
