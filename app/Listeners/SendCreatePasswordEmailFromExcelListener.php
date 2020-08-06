<?php

namespace App\Listeners;

use App\Mail\SendCreatePassword;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Kreait\Firebase\Exception\ApiException;

class SendCreatePasswordEmailFromExcelListener implements ShouldQueue
{

    /**
     * Handle the event.
     *
     * @param object $event
     * @return RedirectResponse
     */
    public function handle($event)
    {
        $users = $event->users;

        foreach ($users[0] as $value) {
            if ($value[0] == 'id')
                continue;
            try {
//                value 5 = email in excel file
                if ($value[5] != null) {
                    $token = Str::random(60);
                    firebaseGetReference('emailedUsers')->push([
                        'token' => $token,
                        'user_id' => $value[0]
                    ]);
                    Mail::to($value[5])->send(new SendCreatePassword($token));
                }

            } catch (ApiException $e) {
                return redirect()->back()->with('error', 'حدثت مشكلة أثناء رفع الملف.');
            }
        }
    }
}
