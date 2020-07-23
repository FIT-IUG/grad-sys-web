<?php

namespace App\Http\Controllers;

class TeacherController extends MainController
{

    public function index()
    {
        $notifications = $this->getNotifications();
        $groups_data = $this->groupsDataForTeacher(null);

        if ($groups_data == null)
            $groups_data = [];

        return view('teacher.index', [
            'notifications' => $notifications,
            'teacher_groups' => $groups_data,
            'message' => ''
        ]);
    }

//    public function replayToBeSupervisorRequest(Request $request)
//    {
//
//        try {
//            $reply = $request->get('reply');
//            $student_std = $request->get('from');
//            $teacher_id = $request->get('to');
//            $key = $request->get('notification_key');
//            $groups = firebaseGetReference('groups')->getValue();
//
//            if ($reply == 'accept') {
//                firebaseGetReference('notifications/' . $key)->update(['status' => 'accept']);
//                foreach ($groups as $index => $group)
//                    if ($group['leaderStudentStd'] == $student_std) {
//                        firebaseGetReference('groups/' . $index)->update(['teacher' => $teacher_id]);
//                        break;
//                    }
//                return redirect()->route(getRole() . '.index')->with('success', 'تم قبول الطلب بنجاح.');
//            } elseif ($reply == 'reject') {
//                firebaseGetReference('notifications/' . $key)->update(['status' => 'reject']);
//                return redirect()->back()->with('success', 'تم رفض الطلب بنجاح.');
//            } else
//                return redirect()->back()->with('error', 'حصلت مشكلة في الطلب.');
//        } catch (ApiException $e) {
//            return redirect()->back()->with('error', 'حصلت مشكلة في الطلب.');
//        }
//    }

}
