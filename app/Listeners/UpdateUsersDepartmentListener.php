<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Kreait\Firebase\Exception\ApiException;

class UpdateUsersDepartmentListener implements ShouldQueue
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
        $old_department = $event->old_department;
        $new_department_name = $event->new_department_name;
        $users = firebaseGetReference('users')->getValue();
        foreach ($users as $user_key => $user) {
            if (isset($user['department']) && $user['department'] != null) {
                if ($user['department'] == $old_department) {
                    firebaseGetReference('users/' . $user_key)->update(['department' => $new_department_name]);
                }
            }
        }
    }
}
