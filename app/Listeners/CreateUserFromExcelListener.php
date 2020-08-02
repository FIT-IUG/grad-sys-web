<?php

namespace App\Listeners;

use App\Mail\SendCreatePassword;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Kreait\Firebase\Exception\ApiException;

class CreateUserFromExcelListener implements ShouldQueue
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
                firebaseGetReference('usersFromExcel')->push([
                    'user_id' => $value[0],
                    'name' => $value[1],
                    'role' => $value[2],
                    'department' => $value[3],
                    'mobile_number' => $value[4],
                    'email' => $value[5],
                ]);

//                $token = Str::random(60);
//                firebaseGetReference('emailedUsers')->push([
//                    'token' => $token,
//                    'user_id' => $value[0]
//                ]);
//                Mail::to($value[5])->send(new SendCreatePassword($token));


            } catch (ApiException $e) {
                return redirect()->back()->with('error', 'حدثت مشكلة أثناء رفع الملف.');
            }
        }
        return 'done';
    }
}
