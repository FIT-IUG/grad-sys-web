<?php

namespace App\Http\Controllers;

use Illuminate\Support\Arr;

class DashboardController extends Controller
{
    public function index()
    {

        $departments = ['تطوير البرمجيات', 'علم الحاسوب', 'نظم المعلومات', 'مالتيميديا', 'موبايل', 'تكنولوجيا المعلومات'];
        $users = firebaseGetReference('users')->getValue();
        $teachers_name = [];
        $index = 0;
        $number_of_students = 0;
        foreach ($users as $user) {
            if ($user['role'] == 'teacher') {
                Arr::set($teachers_name, $index++, $user['name']);
            } elseif ($user['role'] == 'student') {
                $number_of_students++;
            }
        }
        $number_of_groups = sizeof(firebaseGetReference('groups')->getValue());

//        $number_of_groups = collectionSize('groups');
        $number_of_teamed_students = 20;
//        $number_of_teamed_students = numberOfTeamedStudents();
        $statistics = ['number_of_students' => $number_of_students,
            'number_of_groups' => $number_of_groups,
            'number_of_teamed_students' => $number_of_teamed_students];
        $students = '';
//        app('firebase.database')->getReference('notifications/0')->set(['to' => '120125132', 'from' => '120125623', 'type' => 'join_group']);
        $notifications = [];

        if (hasRole('student')) {
//            $groups = firestoreCollection('groups')->documents()->rows();
//            $members_std = Arr::pluck($groups, 'membersStd');
            $students = $this->getStudentsWithoutTeam();
            dd('this is student');

            $std = getUserId();

//        if to == null that is mean there is no notifications
            $studentNotificationTo = firestoreCollection('notifications')->where('to', '=', $std)->documents()->rows();
            if ($studentNotificationTo != null) {
                foreach ($studentNotificationTo as $studentNotification) {
                    $id = $studentNotification->id();
                    $notif = firestoreCollection('notifications')->document($id)->snapshot()->data();
                    if ($notif['isAccept'] == 0) {
                        $from = $notif['from'];
                        $type = $notif['type'];
                        $to = $notif['to'];
                        $from_name = firestoreCollection('users')
                            ->where('role', '=', 'student')
                            ->where('user_id', '=', $from)
                            ->documents()->rows()[0]->data()['name'];
                        if ($type == 'join_team') {
                            $message = 'يطلب منك الطالب ' . $from_name . ' الانضمام الى فريق التخرج الخاص به. اذا كنت موافق اضغط.';
                            $notifications = [['from' => $from, 'message' => $message, 'to' => $to, 'from_name' => $from_name]];
                        }
                    }
                }
            }
        } elseif (hasRole('teacher')) {
//            $teacher_id = getUserId();
            $teacher_id = firebaseGetReference('users/' . session()->get('uid'))->getValue()['user_id'];
            $notifications_for_teacher = firebaseGetReference('notifications')->getValue();
            $i = 0;
            foreach ($notifications_for_teacher as $notification) {
                if ($notification['to'] == $teacher_id){
                    $message = 'طلب لان تكون مشرف للمجموعة.';
                    $from = $notification['from'];
                    $from_name = firebaseGetReference('users')->startAt($from)->getValue();
                    dd($from_name);
//                    $from_name = firestoreCollection('users')->where('role', '=', 'student')
//                        ->where('std', '=', $from)
//                        ->documents()->rows()[0]->data()['name'];
                    $initial_title = firestoreCollection('groups')->where('leaderStudentStd', '=', $from)->documents()->rows()[0]->get('initialProjectTitle');
                    $notifications += [$i++ => ['from' => $from, 'message' => $message, 'to' => $to, 'from_name' => $from_name,
                        'initial_title' => $initial_title]];
                }

            }
        }
        return view('dashboard', ['departments' => $departments, 'teachers' => $teachers_name,
            'statistics' => $statistics, 'students' => $students, 'notifications' => $notifications]);
    }

}
