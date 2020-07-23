<?php

namespace App\Listeners;

use App\Mail\SendCreatePassword;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Kreait\Firebase\Exception\ApiException;

class CreateUserFromExcelListener
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return RedirectResponse
     */
    public function handle($event)
    {
        $users = $event->users;

        foreach ($users[0] as $value) {
            if ($value[0] == 'id')
                continue;
            try {
                firebaseGetReference('users')->push([
                    'user_id' => $value[0],
                    'name' => $value[1],
                    'role' => $value[2],
                    'department' => $value[3],
                    'mobile_number' => $value[4],
                    'email' => $value[5],
                ]);

            } catch (ApiException $e) {
                return redirect()->back()->with('error', 'حصل مشكلة في رفع الملف.');
            }
        }
    }
}
