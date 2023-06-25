<?php

namespace App\Http\Controllers;

use Illuminate\Support\Arr;

class DashboardController extends Controller
{
    public function index()
    {
        $departments = ['تطوير البرمجيات', 'علم الحاسوب', 'نظم المعلومات', 'مالتيميديا', 'موبايل', 'تكنولوجيا المعلومات'];
        $teachers = firestoreCollection('users')
            ->where('role', '=', 'teacher')->documents()->rows();
        $teachers = Arr::pluck($teachers, 'name');
        $number_of_students = firestoreCollection('users')->where('role', '=', 'student')->documents()->size();
        $number_of_groups = collectionSize('groups');
        $number_of_teamed_students = numberOfTeamedStudents();
        $statistics = [
            'number_of_students' => $number_of_students,
            'number_of_groups' => $number_of_groups,
            'number_of_teamed_students' => $number_of_teamed_students
        ];
        $students = '';
        $notifications = [];
        if (hasRole('student')) {
//            $groups = firestoreCollection('groups')->documents()->rows();
//            $members_std = Arr::pluck($groups, 'membersStd');
            $students = $this->getStudentsWithoutTeam();

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
            $to = getUserId();
            $notifications_for_supervisor = firestoreCollection('notifications')
                ->where('to', '=', $to)
                ->where('isAccept', '', 1)->documents()->rows();
            $i = 0;
            foreach ($notifications_for_supervisor as $notification) {
                $message = 'طلب لان تكون مشرف للمجموعة.';
                $from = $notification['from'];
                $from_name = firestoreCollection('users')->where('role', '=', 'student')
                    ->where('std', '=', $from)
                    ->documents()->rows()[0]->data()['name'];
                $initial_title = firestoreCollection('groups')->where('leaderStudentStd', '=', $from)->documents()->rows()[0]->get('initialProjectTitle');
                $notifications += [$i++ => ['from' => $from, 'message' => $message, 'to' => $to, 'from_name' => $from_name,
                    'initial_title' => $initial_title]];
            }
        }
        return view('dashboard', ['departments' => $departments, 'teachers' => $teachers,
            'statistics' => $statistics, 'students' => $students, 'notifications' => $notifications]);
    }

}
