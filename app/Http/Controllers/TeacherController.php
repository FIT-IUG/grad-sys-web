<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Kreait\Firebase\Exception\ApiException;

class TeacherController extends MainController
{

    public function index()
    {
//        $notifications = $this->teacherNotification();
        $notifications = $this->getNotifications();
        $groups_data = $this->groupsData();

        if ($groups_data == null)
            $groups_data = [];

        return view('teacher.index', [
            'notifications' => $notifications,
            'groups_data' => $groups_data,
            'message' => ''
        ]);
    }

    public function replayToBeSupervisorRequest(Request $request)
    {


        $reply = $request->get('reply');
        $student_std = $request->get('from');
        $teacher_id = $request->get('to');
        $key = $request->get('notification_key');
        $groups = firebaseGetReference('groups')->getValue();

        if ($reply == 'accept') {
            firebaseGetReference('notifications/' . $key)->update(['status' => 'accept']);
            foreach ($groups as $index => $group)
                if ($group['leaderStudentStd'] == $student_std) {
                    firebaseGetReference('groups/' . $index)->update(['teacher' => $teacher_id]);
                    break;
                }
            return redirect()->route('teacher.index')->with('success', 'تم قبول الطلب بنجاح.');
        } elseif ($reply == 'reject') {
            firebaseGetReference('notifications/' . $key)->update(['status' => 'reject']);
            return redirect()->back()->with('success', 'تم رفض الطلب بنجاح.');
        } else
            return redirect()->back()->with('error', 'حصلت مشكلة في الطلب.');
    }

//    public function teacherNotification()
//    {
//        try {
//            $groups = firebaseGetReference('groups')->getValue();
//            $user_id = getUserId();
//            $notifications = firebaseGetReference('notifications')->getValue();
//            $user_notifications = [];
//
////          use teacher id to get leader student std
////          use leader student std to get members std
////          use group std to get there data like name and phone number
////          every group has there own data
//
//            foreach ($notifications as $key => $notification) {
//                if ($notification['to'] == $user_id && $notification['status'] == 'wait') {
//                    foreach ($groups as $group) {
//                        if ($group['leaderStudentStd'] == $notification['from']) {
//                            $initialProjectTitle = $group['initialProjectTitle'];
//                            $notification = Arr::collapse([$notification, ['initialProjectTitle' => $initialProjectTitle]]);
//                            Arr::set($user_notifications, $key, $notification);
//                            break;
//                        }
//                    }
//                }
//            }
//            return $user_notifications;
//
//        } catch (ApiException $e) {
//            return null;
//        }
//    }

    public function groupsData()
    {
        $teacher_id = getUserId();
        $groups = firebaseGetReference('groups')->getValue();
        $students = getUserByRole('student');
        $groups_data = [];
        $students_data = [];
        $index = 0;
        $group_counter = 0;

        foreach ($groups as $group) {
            if (isset($group['teacher']) && $group['teacher'] == $teacher_id) {
                $group_students_std = Arr::flatten([$group['leaderStudentStd'], $group['membersStd']]);
                foreach ($students as $student)
                    foreach ($group_students_std as $std)
                        if ($student['user_id'] == $std) {
                            $student = Arr::except($student, ['remember_token', 'role']);
                            if ($group['leaderStudentStd'] == $student['user_id']) {
                                $student = Arr::collapse([$student, ['isLeader' => true]]);
                            } else
                                $student = Arr::collapse([$student, ['isLeader' => false]]);
                            Arr::set($students_data, $index++, $student);
                        }
                $group_data = Arr::collapse([$group, ['students_data' => $students_data]]);
                $students_data = [];
                $index = 0;
                Arr::set($groups_data, $group_counter++, $group_data);
            }
        }
        return $groups_data;
    }
}
