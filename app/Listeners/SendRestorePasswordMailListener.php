<?php

namespace App\Listeners;

use App\Mail\SendRestorePasswordMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Kreait\Firebase\Exception\ApiException;
use Kreait\Firebase\Exception\AuthException;
use Kreait\Firebase\Exception\FirebaseException;

class SendRestorePasswordMailListener implements ShouldQueue
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
        $email = $event->email;
        $token = Str::random(60);
//        $users = firebaseGetReference('users')->getValue();
        $user_key = '';

        try {
            $user_key = firebaseAuth()->getUserByEmail($email)->uid;
        } catch (AuthException $e) {
        } catch (FirebaseException $e) {
        }
//        foreach ($users as $key => $user) {
//            if ($user['email'] == $email) {
//                $user_key = $key;
//                break;
//            }
//        }
        firebaseGetReference('emailedUsers')->push([
            'token' => $token,
            'user_id' => $user_key
         ]);

        Mail::to($email)->send(new SendRestorePasswordMail($token));
    }
}
