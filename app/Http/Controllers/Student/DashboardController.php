<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\MainController;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Kreait\Firebase\Exception\ApiException;
use PhpParser\Node\Expr\Array_;

class DashboardController extends MainController
{

    public function __construct()
    {
        $this->middleware('checkRole');
    }

    public function index()
    {
        $group_info = $this->getGroupInfo();
        $notifications = $this->getNotifications();

//      Check what is the status for student
        if ($group_info['key'] != null) {
            if ($group_info['is_group_leader']) {
                switch ($group_info['status']) {
                    case 'wait_min_members':

                        return view('student.dashboard', [
                            'message' => 'انتظر حتى يوافق الحد الأدنى من أعضاء الفريق',
                            'notifications' => $notifications
                        ]);
                    case 'teacher_reject' :
                    case 'choose_teacher':

                        try {
                            $tags = firebaseGetReference('tags')->getValue();
                            $teachers = $this->getTeachersCanBeSupervisor();

                            return view('student.admin.supervisor_initial_title_form', [
                                'teachers' => $teachers,
                                'notifications' => $notifications,
                                'tags' => $tags,
                            ]);
                        } catch (ApiException $e) {
                            return redirect()->back()->with('error', 'حدثت مشكلة في النظام.');
                        }

                    case 'wait_teacher':

                        return view('student.dashboard', [
                            'notifications' => $notifications,
                            'message' => 'انتظر رد المشرف.'
                        ]);

                    case 'group_complete':

//                       return admin information project information teacher information
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
                            'students' => $students,
                        ]);

                }
            } else {
                switch ($group_info['status']) {
                    case 'wait_min_members':

                        $students = getStudentsStdWithoutGroup();
                        $max_members_number = firebaseGetReference('settings/max_group_members')->getValue();

                        return view('student.admin.create', [
                            'max_members_number' => $max_members_number,
                            'students' => $students,
                            'notifications' => $notifications,
                        ]);
                    case 'wait_teacher' :
                    case 'choose_teacher':

                        return view('student.dashboard', [
                            'message' => 'انتظر انتهاء قائد الفريق من إتمام إعدادات الفريق.',
                            'notifications' => $notifications
                        ]);

                    case 'group_complete':

                        try {
                            $group = firebaseGetReference('groups/' . $group_info['key'])->getValue();
                            $students = getUserByRole('student');
                            $leader_id = $group['leaderStudentStd'];
                            $leader_data = [];
                            $members_data = [];
                            $members_counter = 0;
                            $teacher_data = '';
                            $project_data = Arr::except($group, ['leaderStudentStd', 'status', 'membersStd', 'teacher']);
                            $teachers = getUserByRole('teacher');

                            foreach ($teachers as $teacher) {
                                if ($teacher['user_id'] == $group['teacher']) {
                                    $teacher_data = Arr::except($teacher, ['role', 'remember_token',]);
                                    break;
                                }
                            }

                            foreach ($students as $student_key => $student) {
                                if ($leader_data == null && $student['user_id'] == $leader_id)
                                    $leader_data = Arr::except($student, ['role', 'remember_token']);
                                foreach ($group['membersStd'] as $member) {
                                    if ($student['user_id'] == $member) {
                                        Arr::set($members_data, $student_key, Arr::except($student, ['role', 'remember_token']));
                                        $members_counter++;
                                    }
                                }
                                if (sizeof($group['membersStd']) == $members_counter)
                                    break;
                            }
                            return view('student.member.index', [
                                'notifications' => $notifications,
                                'group_members_data' => $members_data,
                                'group_leader_data' => $leader_data,
                                'teacher_data' => $teacher_data,
                                'project_data' => $project_data,
                            ]);
                        } catch (ApiException $e) {
                            return redirect()->back()->with('error', 'حدثت مشكلة في النظام.');
                        }
                }
            }

        }else{

            $students = getStudentsStdWithoutGroup();
            $max_members_number = firebaseGetReference('settings/max_group_members')->getValue();

            return view('student.admin.create', [
                'max_members_number' => $max_members_number,
                'students' => $students,
                'notifications' => $notifications,
            ]);
        }

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
        return redirect()->route('student.index')->with('success', 'تمت الموافقة على طلب الانضمام بنجاح.');
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

    private function getGroupData($id = 0)
    {
        try {
            $max_members_number = firebaseGetReference('settings/max_group_members')->getValue();
            $group_students_complete = '';
            $group_members_data = [];
            $teacher_data = [];
            $project_data = [];

            if ($id == 0) {
                $groups = firebaseGetReference('groups')->getValue();
                if ($groups != null) {
                    $std = getUserId();

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
                }
            } else {
                $group = firebaseGetReference('groups/' . $id)->getValue();

                $group_students_complete = $max_members_number - sizeof($group['membersStd']);

                $group_members_data = $this->getGroupMembersData($group['membersStd']);

                $teacher_data = $this->getTeacherData($group['teacher']);

                Arr::set($project_data, 0, [
                    'initialProjectTitle' => $group['initialProjectTitle'],
                    'graduateInFirstSemester' => $group['graduateInFirstSemester'],
                    'tags' => $group['tags']
                ]);
            }

            return [
                'group_students_complete' => $group_students_complete,
                'group_members_data' => $group_members_data,
                'teacher_data' => $teacher_data,
                'project_data' => $project_data[0]
            ];
        } catch (ApiException $e) {
            return redirect()->back()->with('error', 'حدثت مشكلة في النظام.');
        }

    }

    public function getGroupInfo()
    {
        try {
            $user_id = getUserId();
            $groups = firebaseGetReference('groups')->getValue();
            $is_group_leader = false;
            $group_key = '';
            $group_status = '';
            if ($groups != null)
                foreach ($groups as $key => $group) {
                    $leadersStd = $group['leaderStudentStd'];
                    if ($leadersStd != null && $leadersStd == $user_id) {
                        $is_group_leader = true;
                        $group_key = $key;
                        $group_status = $group['status'];
                        break;
                    }
                    $members_std = firebaseGetReference('groups/' . $key)->getChild('membersStd')->getValue();

                    if ($group['membersStd'] != null)
                        foreach ($members_std as $member) {
                            if ($member == $user_id) {
                                $group_key = $key;
                                $group_status = $group['status'];
                                break;
                            }
                        }
                    if ($group_key != null)
                        break;
                }
            return [
                'is_group_leader' => $is_group_leader,
                'key' => $group_key,
                'status' => $group_status
            ];
        } catch (\Kreait\Firebase\Exception\ApiException $e) {
        }
    }

//    private function getTeachersCanBeSupervisor()
//    {
//        $teachers = getUserByRole('teacher');
//        $admins = getUserByRole('admin');
//        $teachers = Arr::collapse([$teachers, $admins]);
//        $teacher_counter = 0;
//
//        try {
//            $groups = firebaseGetReference('groups')->getValue();
//            $max_teacher_groups = firebaseGetReference('settings/max_teacher_groups')->getValue();
//            foreach ($teachers as $key => $teacher) {
//                foreach ($groups as $admin) {
//                    if (isset($admin['teacher']) && $teacher['user_id'] == $admin['teacher']) {
//                        $teacher_counter++;
//                    }
//                    if ($teacher_counter == $max_teacher_groups) {
//                        Arr::forget($teachers, $key);
//                    }
//                }
//            }
//            return $teachers;
//        } catch (ApiException $e) {
//            return redirect()->back()->with('error', 'حصلت مشكلة بالنظام.');
//        }
//    }
}
