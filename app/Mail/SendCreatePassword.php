<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendCreatePassword extends Mailable
{
    use Queueable, SerializesModels;

    private string $token;

    /**
     * Create a new message instance.
     *
     * @param $token
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
//        $link = 'https://fit-grad-sys.herokuapp.com/user/create/password/' . $this->token;
        $link = 'http://localhost:8000/user/create/password/' . $this->token;

        return $this->view('mails.student.createPassword')
            ->subject('إنشاء كلمة مرور للحساب')
            ->with(['link' => $link]);
    }
}
