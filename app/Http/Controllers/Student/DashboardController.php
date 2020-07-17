<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\MainController;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Kreait\Firebase\Exception\ApiException;

class DashboardController extends MainController
{

    public function index()
    {


//      Check what is the status for student
        if (inGroup()) {
            if (isGroupLeader()) {
                $notifications = $this->getNotifications();

                if (isMinMembersAccept()) {
                    if (isTeacherHasNotification()) {
                        $teacher_status = getSupervisorStatus();

//                   this case when teacher has notification from the group leader but it's in wait status
                        if ($teacher_status == 'wait') {

                            return view('student.dashboard', [
                                'notifications' => $notifications,
                                'message' => 'انتظر رد المشرف.'
                            ]);
//                        if teacher accept to be supervisor for group
                        } elseif ($teacher_status == 'accept') {

//                       return group information project information teacher information
                            $group_data = $this->getGroupData();

                            $students = '';
                            if ($group_data['group_students_complete'] != 0)
                                $students = getStudentsStdWithoutGroup();

                            return view('student.dashboard', [
                                'notifications' => $notifications,
                                'group_members_data' => $group_data['group_members_data'],
                                'teacher_data' => $group_data['teacher_data'],
                                'project_data' => $group_data['project_data'],
                                'group_students_complete' => $group_data['group_students_complete'],
                                'students' => $students
                            ]);
//                       if teacher refuse or reject to be supervisor for group
                        } else {
//                       return to choose supervisor form
                            return view('student.dashboard', [
                                'notifications' => $notifications,
                                'message' => 'رفض المشرف أن يكون مشرف مجموعتك.'
                            ]);
                        }
                    } else {
//                        send notification form
                        try {
                            $teachers = getUserByRole('teacher');
//                            extra need check
                            $admins = getUserByRole('admin');
                            $teachers = Arr::collapse([$teachers, $admins]);
                            $tags = firebaseGetReference('tags')->getValue();

                            return view('student.group.supervisor_initial_title_form', [
                                'teachers' => $teachers,
                                'notifications' => $notifications,
                                'tags' => $tags
                            ]);
                        } catch (ApiException $e) {
                        }
                    }
                } else {
//                    wait to accept min
                    return view('student.dashboard', [
                        'message' => 'انتظر حتى يوافق الحد الادنى من اعضاء الفريق',
                        'notifications' => $notifications
                    ]);
                }
            } else {
//                member things
                $notifications = $this->getMemberNotifications();

                if ($notifications == 'accept') {
                    try {
                        $groups = firebaseGetReference('groups')->getValue();
                        $student_id = getUserId();
                        foreach ($groups as $group) {
                            foreach ($group['membersStd'] as $std) {
                                if ($std == $student_id)
                                    return view('student.member.index', [
                                        'message' => 'انتظر حتى ينتهي قائد الفريق من اعدادات المشروع.',
                                    ]);
                            }
                        }
                    } catch (ApiException $e) {
                        return redirect()->back()->with('error', 'حصلت مشكلة في النظام.');
                    }

                } else {
                    $students = getStudentsStdWithoutGroup();
                    $max_members_number = firebaseGetReference('settings/max_group_members')->getValue();

                    return view('student.group.create', [
                        'max_members_number' => $max_members_number,
                        'students' => $students,
                        'notifications' => $notifications,
                    ]);
                }
            }
        }

//      create group form for login student
        $students = getStudentsStdWithoutGroup();
        $max_members_number = firebaseGetReference('settings/max_group_members')->getValue();

        return view('student.group.create', [
            'max_members_number' => $max_members_number,
            'students' => $students,
            'notifications' => $this->getNotifications(),
        ]);
    }

    public function acceptTeamJoinRequest(Request $request)
    {

        $from = $request->get('from');
        $to = $request->get('to');

        $notifications = firebaseGetReference('notifications')->getValue();
        foreach ($notifications as $index => $notification) {
            if ($notification['from'] == $from && $notification['to'] == $to) {
                firebaseGetReference('notifications/' . $index)->update(['status' => 'accept']);
            }
        }
        return redirect()->route('student.index')->with('success', 'تم الموافقة على طلب الإنضمام بنجاح.');
    }

    private function getMemberNotifications()
    {

        try {
            $notifications = firebaseGetReference('notifications')->getValue();
            $user_notifications = [];
            $user_id = getUserId();

            if ($notifications != null) {
                foreach ($notifications as $key => $notification) {
                    if ($notification['to'] == $user_id && $notification['type'] == 'join_group') {
                        if ($notification['status'] == 'accept') {
                            return 'accept';
                        } else {
                            if ($notification['status'] == 'wait' xor $notification['status'] == 'readOnce') {

                                Arr::set($user_notifications, $key, $notification);
                                if ($notification['status'] == 'readOnce')
                                    firebaseGetReference('notifications/' . $key)->update(['status' => 'read']);
                            }
                        }
                    }
                }
            }
            return $user_notifications;
        } catch (ApiException $e) {
        }

    }

    private function getGroupData()
    {
        $groups = firebaseGetReference('groups')->getValue();
        $std = getUserId();
        $max_members_number = firebaseGetReference('settings/max_group_members')->getValue();
        $group_students_complete = '';
        $group_members_data = [];
        $teacher_data = [];
        $project_data = [];

        foreach ($groups as $group) {
            if ($group['leaderStudentStd'] == $std) {
                $members_std = $group['membersStd'];
                $group_students_complete = $max_members_number - sizeof($members_std);

                $group_members_data = $this->getGroupMembersData($members_std);

                $teacher_data = $this->getTeacherData($group['teacher']);

                Arr::set($project_data, 0, [
                    'initialProjectTitle' => $group['initialProjectTitle'],
                    'graduateInFirstSemester' => $group['graduateInFirstSemester'],
                    'tags' => $group['tags']
                ]);
                break;
            }
        }

        return [
            'group_students_complete' => $group_students_complete,
            'group_members_data' => $group_members_data,
            'teacher_data' => $teacher_data,
            'project_data' => $project_data[0]
        ];

    }

    private function getGroupMembersData($members_std)
    {
        $group_members_data = [];
        $students = getUserByRole('student');
        $index = 0;

        foreach ($students as $student) {
            foreach ($members_std as $std)
                if ($student['user_id'] == $std) {
                    $student = Arr::except($student, ['remember_token', 'role', 'department']);
                    Arr::set($group_members_data, $index++, $student);
                    break;
                }
            if (sizeof($members_std) == $index)
                break;
        }
        return $group_members_data;
    }

    private function getTeacherData($teacher_id)
    {
        $teachers = getUserByRole('teacher');
        $teacher_data = [];

        foreach ($teachers as $teacher)
            if ($teacher['user_id'] == $teacher_id) {
                $teacher_data = Arr::except($teacher, ['role', 'user_id', 'remember_token']);
                break;
            }
        return $teacher_data;
    }


}
