<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserPassword extends Mailable
{
    use Queueable, SerializesModels;

    /**
    * The user instance.
    *
    * @var User
    */
    public $user;

    /**
    * The username instance.
    *
    * @var User
    */
    public $username;

    /**
    * The verification code.
    *
    * @var String
    */
    public $password;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $username, $password)
    {
        $this->user = $user;
        $this->username = $username;
        $this->password = $password;
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
        ->view('emails.user_password')->text('emails.user_password_plain');

    }
}
