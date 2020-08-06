<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendRestorePasswordMail extends Mailable
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
        $link = 'https://fit-grad-sys.herokuapp.com/user/edit/password/' . $this->token;
//        $link = 'http://localhost:8000/user/edit/password/' . $this->token;

        return $this->view('mails.student.restorePassword')
            ->subject('تغيير كلمة المرور')
            ->with(['link' => $link]);
    }
}
