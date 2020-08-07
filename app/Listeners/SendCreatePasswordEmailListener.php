<?php

namespace App\Listeners;

use App\Mail\SendCreatePassword;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Kreait\Firebase\Exception\ApiException;

class SendCreatePasswordEmailListener implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     * @throws ApiException
     */
    public function handle($event)
    {
        $key = $event->key;
        $email = $event->student_email;
        //Send Email
        $token = Str::random(60);
        firebaseGetReference('emailedUsers')->push([
            'user_id' => $key,
            'token' => $token
        ]);

        Mail::to($email)->send(new SendCreatePassword($token));

    }
}
