<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SMSController extends Controller
{
    /**
     * Username.
     *
     * @var string
     */
    protected $url = "http://mobixad.com/api/sendsms";
    /**
     * Username.
     *
     * @var string
     */
    protected $username = 'COGSNET';
    /**
     * Password.
     *
     * @var string
     */
    protected $password = '123456';
    /**
     * Type.
     *
     * @var string
     */
    protected $type = '0';
    /**
     * Sender.
     *
     * @var string
     */
    protected $sender = 'NBTP';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        // $this->middleware('user');
    }

    /**
     * Send messages
     *
     * @param string $reciever
     * @param string $message
     * @return string $output
     */
    static function send($receiver, $message) {

        $self = new static;

        $username = urlencode($self->username);
        $password = urlencode($self->password);
        $sender = urlencode($self->sender);

        $receiver = urlencode($receiver);
        $message = urlencode($message);

        $url = $self->url."?username=".$username."&password=".$password."&message=".$message."&receiver=".$receiver."&sender=".$sender;

        $curl_ch = curl_init();
        curl_setopt($curl_ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl_ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($curl_ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($curl_ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl_ch, CURLOPT_URL, $url);

        return curl_exec($curl_ch);

    }

}
