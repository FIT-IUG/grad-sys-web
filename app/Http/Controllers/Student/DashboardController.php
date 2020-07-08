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

        if (inGroup()) {
            if (isGroupLeader()) {
                if (isMinMembersAccept()) {
                    if (isTeacherHasNotification()) {
                        $teacher_status = getSupervisorNotificationStatus();

//                    this case when teacher has notification from the group leader but it's in wait status
                        if ($teacher_status == 'wait') {

                            return view('student.dashboard', [
                                'notifications' => $notifications,
                                'message' => 'انتظر رد المشرف.'
                            ]);
//                        if teacher accept to be supervisor for group
                        } elseif ($teacher_status == 'accept') {
//                       return group information project information teacher information
                            $group_data = [];
                            $groups = firebaseGetReference('groups')->getValue();
                            $students = getUserByRole('student');
                            $teachers = getUserByRole('teacher');
                            $std = getUserId();
                            $index = 0;
                            $teacher_data = [];
                            $project_data = [];
                            $max_members_number = firebaseGetReference('settings/max_group_members')->getValue();
                            $group_students_complete = '';

                            foreach ($groups as $group) {
                                if ($group['leaderStudentStd'] == $std) {
//                                    $group_std = Arr::flatten([$group['leaderStudentStd'], $group['membersStd']]);
                                    $members_std = $group['membersStd'];
                                    $group_students_complete = $max_members_number - sizeof($members_std);
//                                    $project_title = $group['initialProjectTitle'];
                                    foreach ($students as $student) {
                                        foreach ($members_std as $std)
                                            if ($student['user_id'] == $std) {
                                                $student = Arr::except($student, ['remember_token', 'role', 'department']);
                                                Arr::set($group_data, $index++, $student);
                                                break;
                                            }
                                        if (sizeof($members_std) == $index)
                                            break;
                                    }
                                    $teacher_id = $group['teacher'];
                                    foreach ($teachers as $teacher)
                                        if ($teacher['user_id'] == $teacher_id) {
                                            $teacher_data = Arr::except($teacher, ['role', 'user_id', 'remember_token']);
                                            break;
                                        }
                                    $index = 0;
                                    Arr::set($project_data, $index, [
                                        'initialProjectTitle' => $group['initialProjectTitle'],
                                        'graduateInFirstSemester' => $group['graduateInFirstSemester'],
                                        'tags' => $group['tags']
                                    ]);
                                    break;
                                }
                            }

                            $students = getStudentsStdWithoutGroup();

                            return view('student.dashboard', [
                                'notifications' => $notifications,
                                'group_data' => $group_data,
                                'teacher_data' => $teacher_data,
                                'project_data' => $project_data[0],
                                'group_students_complete' => $group_students_complete,
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
                if ($student_status == 'reject' || $student_status === null) {
                    $students = getStudentsStdWithoutGroup();
                    $max_members_number = firebaseGetReference('settings/max_group_members')->getValue();

                    return view('student.group.create', [
                        'max_members_number' => $max_members_number,
                        'students' => $students,
                        'notifications' => $notifications,
                    ]);
//                if student have notification put not accept or reject it (wait status)
                } elseif ($student_status == 'wait') {

                    return view('student.dashboard', [
                        'notifications' => $notifications,
                        'message' => null
                    ]);
                } else {

                    try {
                        $groups = firebaseGetReference('groups')->getValue();
                        $student_id = getUserId();
                        foreach ($groups as $group) {
                            foreach ($group['membersStd'] as $std) {
                                if ($std == $student_id) {
                                    if (isset($group['teacher'])) {
                                        return view('student.dashboard',
                                            [
                                                '' => '',
                                            ]);
                                    }
                                }
                            }
                        }
                    } catch (ApiException $e) {
                    }

                    return view('student.dashboard', [
                        'notifications' => $notifications,
                        'message' => 'انتظر حتى ينتهي قائد الفريق من اعدادات المشروع'
                    ]);
                }
            }
        }
//            create group form
        $students = getStudentsStdWithoutGroup();
        $max_members_number = firebaseGetReference('settings/max_group_members')->getValue();

        return view('student.group.create', [
            'max_members_number' => $max_members_number,
            'students' => $students,
            'notifications' => $notifications,
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

    private function getUserNotifications()
    {

        $user_notifications = firebaseGetReference('notifications')->getValue();
        $notifications = [];
        $user_id = getUserId();
        $groups = firebaseGetReference('groups')->getValue();
        $project_initial_title = '';

        if ($user_notifications != null) {
            foreach ($user_notifications as $key => $notification) {
                if ($notification['to'] == $user_id && ($notification['status'] == 'wait' || $notification['status'] == 'readOnce')) {
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
                        Arr::set($notifications, $key, $teacher_notification);
                    } else
                        Arr::set($notifications, $key, $notification);
                }
            }
        }

        return $notifications;
    }

}
