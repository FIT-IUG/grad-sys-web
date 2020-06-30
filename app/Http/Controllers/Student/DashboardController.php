<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGroupMembersRequest;
use App\Http\Requests\StoreGroupRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Kreait\Firebase\Exception\ApiException;

class DashboardController extends Controller
{

    public function index()
    {
        $student_status = isMemberHasNotification();
        $notifications = $this->getUserNotifications();

        if (!inGroup()) {
            if ($student_status == -1 || $student_status === null) {
                $students = getStudentsStdWithoutGroup();
                $max_members_number = firebaseGetReference('settings/max_group_members')->getValue();

                return view('student.group.create', [
                    'max_members_number' => $max_members_number,
                    'students' => $students,
                    'notifications' => $notifications,
                ]);
            } elseif ($student_status == 0) {

                return view('student.dashboard', [
                    'notifications' => $notifications,
                ]);
            }
        } elseif (isGroupLeader()) {
            if (isMinMembersAccept()) {
                if (!isTeacherHasNotification()) {
                    try {
                        $teacher = getUserByRole('teacher');
                        $tags = firebaseGetReference('tags')->getValue();

                        return view('student.group.supervisor_initial_title_form', [
                            'teachers' => $teacher,
                            'notifications' => $notifications,
                            'tags' => $tags
                        ]);
                    } catch (ApiException $e) {
                    }
                } else {
                    $teacher_status = getSupervisorNotificationStatus();
                    if ($teacher_status == 0) {
//                        foreach ($notifications as $notification){
//                            dd(Arr::get($notification, 'type'));
//                        }
                        return view('student.dashboard', [
                            'notifications' => $notifications,
                            'message' => 'انتظر رد المشرف.'
                        ]);
                    } elseif ($teacher_status == 1) {

                        return view('student.dashboard', [
                            'notifications' => $notifications,
                            'message' => 'وافق المشرف على أن يكون مشرف مجموعتك.'
                        ]);
                    } else {

                        return view('student.dashboard', [
                            'notifications' => $notifications,
                            'message' => 'رفض المشرف أن يكون مشرف مجموعتك.'
                        ]);
                    }
                }
            } else {
                $message = 'انتظر حتى يوافق الحد الادنى من اعضاء الفريق';

                return view('student.dashboard', [
                    'message' => $message,
                    'notifications' => $notifications]);
            }
        } else {
//            firebaseGetReference('tags')->push('علم الحاسوب');
            $teacher = getUserByRole('teacher');
            $tags = firebaseGetReference('tags')->getValue();

            return view('student.dashboard', [
                'teachers' => $teacher,
                'notifications' => $notifications,
                'tags' => $tags
            ]);
        }
    }


    public function storeGroupMembers(StoreGroupMembersRequest $request)
    {
//        $group_data = Arr::except($request->validated(), 'membersStd');
//        try {
//            firebaseGetReference('groups')->push($group_data);
//            $students = getUserByRole('student');
//            $leader_std = $request->get('leaderStudentStd');
//            $leader_name = '';
//            foreach ($students as $student) {
//                if ($student['user_id'] == $leader_std) {
//                    $leader_name = $student['name'];
//                    break;
//                }
//            }
//
//            //notification for every member to join group by event
//            $members_std = $request->validated()['membersStd'];
//
//            foreach ($members_std as $member_std) {
//                firebaseGetReference('notifications')->push([
//                    'from' => $request->get('leaderStudentStd'),
//                    'from_name' => $leader_name,
//                    'to' => $member_std,
//                    'type' => 'join_team',
//                    'message' => 'طلب منك الطالب ' . $leader_name . ' الانضمام الى فريق التخرج الخاص بيه.',
//                    'status' => 0,
//                ]);
//            }
//
//            return redirect()->back()->with('success', 'تم ارسال الطلبات لاعضاء المجموعة.');
//        } catch (ApiException $e) {
//        }
    }

    public function acceptTeamJoinRequest()
    {
        $from = request()->get('from');
        $to = request()->get('to');

        $notifications = firebaseGetReference('notifications')->getValue();
        foreach ($notifications as $index => $notification) {
            if ($notification['from'] == $from && $notification['to'] == $to) {
                firebaseGetReference('notifications/' . $index)->update(['isAccept' => '1']);
            }
        }
//        dd('hello there');
//        $hh = firestoreCollection('notifications')
//            ->where('from', '=', request()->get('from'))
//            ->where('to', '=', request()->get('to'));
//        $id = $hh->documents()->rows()[0]->id();
//        firestoreCollection('notifications')->document($id)->update([['path' => 'isAccept', 'value' => 1]]);
        return redirect()->back()->with('success', 'تم الموافقة على طلب الإنضمام بنجاح.');
    }

    public function storeGroupSupervisor(StoreGroupRequest $request)
    {
        $leader_data = firebaseGetReference('users/' . session()->get('uid'))->getValue();
        $leader_id = $leader_data['user_id'];
        $leader_name = $leader_data['name'];
        $groups = firebaseGetReference('groups')->getValue();

        foreach ($groups as $key => $group) {
            if ($group['leaderStudentStd'] == $leader_id) {
                firebaseGetReference('groups/' . $key)->update($request->validated());
                break;
            }
        }
        firebaseGetReference('notifications')->push([
            'from' => $leader_id,
            'from_name' => $leader_name,
            'to' => $request->get('teacher'),
            'message' => 'طلب منك الطالب ' . $leader_name . 'أن تكون مشرف فريقه.',
            'project_initial_title' => $request->get('initial_title'),
            'type' => 'to_be_supervisor',
            'isAccept' => '0',
        ]);
        return redirect()->back()->with('success', 'تم ارسال الطلب بنجاح');
    }

    private function getUserNotifications()
    {
//        dd(getUserId());

        $user_notifications = firebaseGetReference('notifications')->getValue();
        $notifications = [];
        $index = 0;
        $user_id = getUserId();
        $groups = firebaseGetReference('groups')->getValue();
        $project_initial_title = '';
        if ($user_notifications != null) {
            foreach ($user_notifications as $notification) {
                if ($notification['to'] == $user_id && $notification['status'] == '0') {
//               this type of notification to teacher and need with normal data in notification a project initial title
                    if ($notification['type'] == 'to_be_supervisor') {
//                    get project initial title
                        foreach ($groups as $group)
                            if ($group['leaderStudentStd'] == $notification['from'])
                                $project_initial_title = $group['project_initial_title'];

                        $teacher_notification = Arr::collapse([
                            $notification,
                            ['initial_title' => $project_initial_title]
                        ]);
                        Arr::set($notifications, $index++, $teacher_notification);
                    } else
                        Arr::set($notifications, $index++, $notification);
                }
            }
        }

        return $notifications;
    }


//    public function index()
//    {
//
////        $this->studentStatus();
//        if (!inGroup()) {
//
//            $students = getStudentsStdWithoutGroup();
//
//            return view('student.group.create', [
////                'departments' => $departments,
////                'teachers' => $teacher,
////                'statistics' => $statistics,
//                'students' => $students,
//                'notifications' => $this->getUserNotifications(),
////                'tags' => $tags
//            ]);
//
//        }
//
//        $departments = ['تطوير البرمجيات', 'علم الحاسوب', 'نظم المعلومات', 'مالتيميديا', 'موبايل', 'تكنولوجيا المعلومات'];
//        $teacher = [];
//        $index = 0;
//        $number_of_students = 0;
//        $user_id = getUserId();
////        $student_gender = Str::substr($user_id, 0, 1);
////        $students = [];
//        $tags = ['تطبيق أندرويد', 'تطبيق IOS', 'موقع ويب', 'فلم قصير', 'فلم أنيميشن', 'لعبة حاسوب',];
//        $users = firebaseGetReference('users')->getValue();
//
//        foreach ($users as $user) {
//            if ($user['role'] == 'teacher')
////                add teachers to array with there names and ids to use in register in group for students
//                Arr::set($teacher, $index++, ['name' => $user['name'], 'id' => $user['user_id']]);
//            elseif ($user['role'] == 'student')
//                $number_of_students++;
//        }
//
//        //Check if registered student is male(1) or female(2) by first number of there std
//        $students = getStudentsStdWithoutGroup();
//
//        $groups = firebaseGetReference('groups')->getValue();
//        $number_of_groups = $groups != null ? sizeof($groups) : 0;
//
//        $number_of_teamed_students = 20;
//        $statistics = [
//            'number_of_students' => $number_of_students,
//            'number_of_groups' => $number_of_groups,
//            'number_of_teamed_students' => $number_of_teamed_students
//        ];
//
//        // get user id, every user have unique id
//
//        return 'hello in student dashboard controller';
//
////        return view('dashboard', [
////            'departments' => $departments,
////            'teachers' => $teacher,
////            'statistics' => $statistics,
////            'students' => $students,
////            'notifications' => $this->getUserNotifications(),
////            'tags' => $tags
////        ]);
//    }
//
//    private function studentStatus()
//    {
//        $std = getUserId();
//
//        $notifications = firebaseGetReference('notifications')->getValue();
////        $user_notifications = [];
////        $index = 0;
//        foreach ($notifications as $notification) {
//            if ($notification['to'] == $std) {
//                if ($notification['status'] == 0) {
//                    return redirect()->route('student.wait');
////                    dd('wait status notification');
//                } elseif ($notification['status'] == 1) {
//                    return redirect()->route('student.accept');
////                    dd('accept status notification');
//                } elseif ($notification['status'] == -1) {
//                    return redirect()->route('student.reject');
////                    dd('reject status notification');
//                }
////                Arr::set($user_notifications, $index++, $notification);
//            }
//        }
//
//        dd($notifications);
//    }
//
//    public function wait()
//    {
//
//        return;
//    }
//
//    private function getUserNotifications()
//    {
//
//        $user_notifications = firebaseGetReference('notifications')->getValue();
//        $notifications = [];
//        $index = 0;
//        $user_id = getUserId();
//        $groups = firebaseGetReference('groups')->getValue();
//        $project_initial_title = '';
//        foreach ($user_notifications as $notification) {
//            if ($notification['to'] == $user_id && $notification['status'] == '0') {
////               this type of notification to teacher and need with normal data in notification a project initial title
//                if ($notification['type'] == 'to_be_supervisor') {
////                    get project initial title
//                    foreach ($groups as $group)
//                        if ($group['leaderStudentStd'] == $notification['from'])
//                            $project_initial_title = $group['project_initial_title'];
//
//                    $teacher_notification = Arr::collapse([
//                        $notification,
//                        ['initial_title' => $project_initial_title]
//                    ]);
//                    Arr::set($notifications, $index++, $teacher_notification);
//                } else
//                    Arr::set($notifications, $index++, $notification);
//            }
//        }
//        return $notifications;
//    }
//
//    public function createGroup()
//    {
//
//    }
}
