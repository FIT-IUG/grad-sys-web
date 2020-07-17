<?php

namespace App\Http\Controllers;


use Exception;
use Illuminate\Support\Arr;

class MainController extends Controller
{
    public function getNotifications()
    {
        try {

            $notifications = firebaseGetReference('notifications')->getValue();
            $user_notifications = [];
            $user_id = getUserId();
            $teacher_notification = [];

            if ($notifications != null) {
                foreach ($notifications as $key => $notification) {
                    if ($notification['to'] == $user_id && ($notification['status'] == 'wait' xor $notification['status'] == 'readOnce')) {
//               this type of notification to teacher and need with normal data in notification a project initial title
//                       to be supervisor notification type send for teacher and admin
                        if ($notification['type'] == 'to_be_supervisor') {
                            $groups = firebaseGetReference('groups')->getValue();
//                      get project initial title
                            foreach ($groups as $group)
                                if ($group['leaderStudentStd'] == $notification['from']) {
                                    $teacher_notification = Arr::collapse([
                                        $notification, ['initial_title' => $group['project_initial_title']]
                                    ]);
                                    break;
                                }
                            Arr::set($user_notifications, $key, $teacher_notification);
                        } else
                            Arr::set($user_notifications, $key, $notification);
                        if ($notification['status'] == 'readOnce')
                            firebaseGetReference('notifications/' . $key)->update(['status' => 'read']);
                    }
                }
            }

            return $user_notifications;

        } catch (Exception $exception) {
            return null;
        } catch (\Kreait\Firebase\Exception\ApiException $e) {
        }
    }
}
