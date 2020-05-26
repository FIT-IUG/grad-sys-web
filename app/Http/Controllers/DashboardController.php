<?php

namespace App\Http\Controllers;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index()
    {

        $departments = ['تطوير البرمجيات', 'علم الحاسوب', 'نظم المعلومات', 'مالتيميديا', 'موبايل', 'تكنولوجيا المعلومات'];
        $teacher = [];
        $index = 0;
        $number_of_students = 0;
        $user_id = getUserId();
        $student_gender = Str::substr($user_id, 0, 1);
        $students = [];
        $tags = ['تطبيق أندرويد', 'تطبيق IOS', 'موقع ويب', 'فلم قصير', 'فلم أنيميشن', 'لعبة حاسوب',];
        $users = firebaseGetReference('users')->getValue();
        foreach ($users as $user) {
            if ($user['role'] == 'teacher') {
//                add teachers to array with there names and ids to use in register in group for students
                Arr::set($teacher, $index++, ['name' => $user['name'], 'id' => $user['user_id']]);
            } elseif ($user['role'] == 'student') {
//                Counter for number of students
                $number_of_students++;
                //Check if registered student is male(1) or female(2) by first number of there std
                if (Str::startsWith($user['user_id'], $student_gender))
                    Arr::set($students, $index++, $user['user_id'] . '');
            }
        }
        $groups = firebaseGetReference('groups')->getValue();
        $number_of_groups = $groups != null ? sizeof($groups) : 0;

        $number_of_teamed_students = 20;
        $statistics = [
            'number_of_students' => $number_of_students,
            'number_of_groups' => $number_of_groups,
            'number_of_teamed_students' => $number_of_teamed_students
        ];
        $notifications = [];

        // get user id, every user have unique id

        $user_notifications = firebaseGetReference('notifications')->getValue();
        $index = 0;
        foreach ($user_notifications as $notification) {
            if ($notification['to'] == $user_id && $notification['isAccept'] == '0') {
                if ($notification['type'] == 'to_be_supervisor') {
                    $students_data = getUserByRole('student');
                    $from_id = $notification['from'];
                    $from_name = $notification['from_name'];
                    $project_initial_title = $notification['project_initial_title'];
//                    foreach ($students_data as $student) {
//                        if ($student['user_id'] == $from_id) {
//                            $from_name = $student['name'];
//                            break;
//                        }
//                    }
                    $teacher_notification = Arr::collapse([
                        $notification,
                        ['from_name' => $from_name, 'initial_title' => $project_initial_title]
                    ]);
                    Arr::set($notifications, $index++, $teacher_notification);
                } else
                    Arr::set($notifications, $index++, $notification);
            }
        }

        return view('dashboard', [
            'departments' => $departments,
            'teachers' => $teacher,
            'statistics' => $statistics,
            'students' => $students,
            'notifications' => $notifications,
            'tags' => $tags
        ]);
    }

}
