<?php

namespace App\Listeners;

use App\Mail\SendCreatePassword;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Kreait\Firebase\Exception\ApiException;

class SendCreatePasswordEmailFromExcelListener
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
                $token = Str::random(60);
                Mail::to($value[5])->send(new SendCreatePassword($token));

            } catch (ApiException $e) {
                return redirect()->back()->with('error', 'حدثت مشكلة أثناء رفع الملف.');
            }
        }
    }
}
