<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\RedirectResponse;
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
            } catch (ApiException $e) {
                return redirect()->back()->with('error', 'حدثت مشكلة أثناء رفع الملف.');
            }
        }
    }
}
