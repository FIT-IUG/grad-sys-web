<?php

namespace App\Http\Controllers;

use App\Events\TestEvent;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Kreait\Firebase\Exception\ApiException;
use Kreait\Firebase\Exception\AuthException;
use Kreait\Firebase\Exception\FirebaseException;

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
//                       to be supervisor notification type send for teacher and leader
                        if ($notification['type'] == 'to_be_supervisor') {
                            $groups = firebaseGetReference('groups')->getValue();
//                      get project initial title
                            if ($groups != null)
                                foreach ($groups as $group) {
                                    if ($group['leaderStudentStd'] == $notification['from']) {
                                        $teacher_notification = Arr::collapse([$notification, [
                                            'initialProjectTitle' => $group['initialProjectTitle'],
                                            'tags' => $group['tags'],
                                            'members_names' => $this->getGroupMembersDataForNotifications($group['membersStd'], $group['leaderStudentStd'])
                                        ]]);
                                        break;
                                    }
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

    function generateRandomNumber($digits = 4)
    {
        $i = 0; //counter
        $pin = ""; //our default pin is blank.
        while ($i < $digits) {
            //generate a random number between 0 and 9.
            $pin .= mt_rand(0, 9);
            $i++;
        }
        return $pin;
    }

    public function create()
    {
//        for ($i = 0; $i < 22; $i++) {
//            $uid = firebaseAuth()->listUsers()->current()->uid;
//            firebaseAuth()->deleteUser($uid);
//        }

//        createUsers();
//        dd('leader created');
        $role = 'teacher';
        $password = $role . '123';
        $departments = firebaseGetReference('departments')->getValue();
        try {
            for ($index = 0; $index <= 50; $index++) {
                $uid = firebaseAuth()->createUserWithEmailAndPassword($role . '' . $index . '@example.com', $password)->uid;
                firebaseGetReference('users/' . $uid)->set([
                    'email' => $role . '' . $index . '@example.com',
                    'name' => 'teacher' . $index,
                    'role' => $role,
                    'mobile_number' => '059' . $this->generateRandomNumber(7),
                    'user_id' => '12016' . $this->generateRandomNumber(),
                    'department' => 'FIT'
                ]);
            }
            return 'users created successfully';
        } catch (AuthException $e) {
        } catch (FirebaseException $e) {
        } catch (Exception $e) {
        }

    }

    protected function groupsDataForTeacher($id)
    {

        try {
            $teacher_id = $id;
            if ($id == null)
                $teacher_id = getUserId();
            $groups = firebaseGetReference('groups')->getValue();
            $groups_data = [];

            if ($groups != null)
                foreach ($groups as $key => $group) {
                    if (isset($group['teacher']) && $group['teacher'] == $teacher_id) {
                        $group_data = $this->getAllGroupInfoForTeacher($key);
                        Arr::set($groups_data, $key, $group_data);
                    }
                }
            return $groups_data;
        } catch (ApiException $e) {
        }

    }

    protected function getGroupMembersData($members_std, $leader_id = 0)
    {
        $group_members_data = [];
        $students = getUserByRole('student');
        $index = 0;
        if ($leader_id == 0) {
            if ($students != null)
                foreach ($students as $key => $student) {
                    foreach ($members_std as $std)
                        if ($student['user_id'] == $std) {
                            $student = Arr::except($student, ['remember_token', 'role']);
                            Arr::set($group_members_data, $key, $student);
                            $index++;
                            break;
                        }
                    if (sizeof($members_std) == $index)
                        break;
                }
            return $group_members_data;
        } else {
            $leader_data = [];
            if ($students != null)
                foreach ($students as $key => $student) {
                    if ($student['user_id'] == $leader_id) {
                        $leader_data = Arr::except($student, ['remember_token', 'role']);
                        Arr::set($leader_data, 'key', $key);
                    }
                    foreach ($members_std as $std) {
                        if (isset($student['user_id']) && $student['user_id'] == $std) {
                            $student = Arr::except($student, ['remember_token', 'role']);
                            Arr::set($group_members_data, $key, $student);
                            $index++;
                            break;
                        }
                    }
                    if (sizeof($members_std) == $index && $leader_data != null) {
                        break;
                    }
                }
            return [
                'leader_data' => $leader_data,
                'members_data' => $group_members_data
            ];
        }

    }

    private function getGroupMembersDataForNotifications($members, $leader)
    {
        $group_data = $this->getGroupMembersData($members, $leader);
//        dd($group_data);
        $excepted_info = ['email', 'mobile_number'];
//        $leader_data = Arr::except($group_data['leader_data'], $excepted_info);
        $members_data = [];
        Arr::set($members_names, $group_data['leader_data']['key'], $group_data['leader_data']['name']);
//        dd($members_names);
        foreach ($group_data['members_data'] as $key => $member) {
//            $excepted_member_info = Arr::except($member, $excepted_info);
            Arr::set($members_names, $key, $member['name']);
//            Arr::set($members_data, $key, $excepted_member_info);
        }
//        dd($members_names);
        return
//            'leader_data' => $leader_data,
//            'members_data' => $members_data
            $members_names;
    }

    protected function getTeacherData($teacher_id)
    {
        $teachers = getUserByRole('teacher');
        $admins = getUserByRole('admin');
        $teachers = Arr::collapse([$teachers, $admins]);
        $teacher_data = [];

        foreach ($teachers as $teacher)
            if ($teacher['user_id'] == $teacher_id) {
                $teacher_data = Arr::except($teacher, ['role', 'remember_token']);
                break;
            }
        return $teacher_data;
    }

    public function replayToBeSupervisorRequest(Request $request)
    {
        try {
            $reply = $request->get('reply');
            $student_std = $request->get('from');
            $teacher_id = $request->get('to');
            $key = $request->get('notification_key');
            $groups = firebaseGetReference('groups')->getValue();
            $group_key = '';

            if ($groups != null)
                foreach ($groups as $index => $group)
                    if ($group['leaderStudentStd'] == $student_std) {
                        $group_key = $index;
                        break;
                    }

            if ($reply == 'accept') {
                firebaseGetReference('notifications/' . $key)
                    ->update(['status' => 'accept']);

                firebaseGetReference('groups/' . $group_key)
                    ->update(['teacher' => $teacher_id, 'status' => 'group_complete']);
                return redirect()->route(getRole() . '.index')->with('success', 'تم قبول الطلب بنجاح.');
            } elseif ($reply == 'reject') {
                firebaseGetReference('notifications/' . $key)->update(['status' => 'reject']);
                firebaseGetReference('groups/' . $group_key)->update(['status' => 'teacher_reject']);
                return redirect()->route(getRole() . '.index')->with('success', 'تم رفض الطلب بنجاح.');
            } else
                return redirect()->route(getRole() . '.index')->with('error', 'حصلت مشكلة في الطلب.');
        } catch (ApiException $e) {
            return redirect()->route(getRole() . '.index')->with('error', 'حصلت مشكلة في الطلب.');
        }
    }

    protected function getAllGroupInfoForTeacher($group_key)
    {
        try {
            $group = firebaseGetReference('groups/' . $group_key)->getValue();

            if ($group != null) {
                if (is_array($group['membersStd']))
                    $group_members_data = $this->getGroupMembersData($group['membersStd'], $group['leaderStudentStd']);
                else
                    $group_members_data = $this->getGroupMembersData([$group['membersStd']], $group['leaderStudentStd']);

                if (isset($group['teacher']))
                    $teacher_data = $this->getTeacherData($group['teacher']);
                else
                    $teacher_data = null;

                if (isset($group['initialProjectTitle'])) {
                    Arr::set($project_data, 0, [
                        'initialProjectTitle' => $group['initialProjectTitle'],
                        'graduateInFirstSemester' => $group['graduateInFirstSemester'],
                        'tags' => $group['tags']
                    ]);
                    $project_data = $project_data[0];
                } else
                    $project_data = null;

                return [
                    'group_leader_data' => $group_members_data['leader_data'],
                    'group_members_data' => $group_members_data['members_data'],
                    'teacher_data' => $teacher_data,
                    'project_data' => $project_data,
                    'students' => getStudentsStdWithoutGroup(),
                    'group_key' => $group_key
                ];
            } else
                return [];

        } catch (ApiException $e) {
            return redirect()->route(getRole() . 'index')->with('error', 'حدثت مشكلة أثناء جلب بيانات المجموعات.');
        }

    }

    public function getTeachersCanBeSupervisor($teacher_id = 0)
    {
        $teachers = getUserByRole('teacher');
        $admins = getUserByRole('admin');
        $teachers = Arr::collapse([$teachers, $admins]);
        $teacher_counter = 0;

        try {
            $groups = firebaseGetReference('groups')->getValue();
            $max_teacher_groups = firebaseGetReference('settings/max_teacher_groups')->getValue();

            if ($groups != null)
                foreach ($teachers as $key => $teacher) {
                    foreach ($groups as $group) {
                        if (isset($group['teacher']) && $teacher['user_id'] == $group['teacher']) {
                            $teacher_counter++;
                        }
                        if ($teacher['user_id'] == $teacher_id)
                            Arr::forget($teachers, $key);
                        if ($teacher_counter == $max_teacher_groups) {
                            Arr::forget($teachers, $key);
                        }
                    }
                }
            return $teachers;
        } catch (ApiException $e) {
            return redirect()->back()->with('error', 'حدثت مشكلة فيل النظام.');
        }
    }

    protected function getGroupMembersDepartments()
    {
        $departments = firebaseGetReference('departments')->getValue();
        $departments_to_send = [];
        foreach ($departments as $department)
            Arr::set($departments_to_send, $department, 0);
        try {
            $groups = firebaseGetReference('groups')->getValue();
            if ($groups != null)
                foreach ($groups as $group) {
                    $membersStd = [];
                    if ($group['membersStd'] != null)
                        $membersStd = $group['membersStd'];
                    $data = $this->getGroupMembersData($membersStd, $group['leaderStudentStd']);
                    if ($data['leader_data']['department'] != null)
                        $departments_to_send[$data['leader_data']['department']]++;
                    foreach ($data['members_data'] as $member)
                        if ($member['department'] != null)
                            $departments_to_send[$member['department']]++;
                }
            return $departments_to_send;
        } catch (ApiException $e) {
        }
    }

    protected function getNumberOfStudentsInDepartments()
    {
        $departments = firebaseGetReference('departments')->getValue();
        $departments_to_send = [];
        foreach ($departments as $department_key => $department)
            if (strtoupper($department) == 'FIT')
                Arr::forget($departments, $department_key);
            else
                Arr::set($departments_to_send, $department, 0);

        try {
            $students = getUserByRole('student');
            if ($students != null)
                foreach ($students as $student) {
                    if (isset($student['department']) && $student['department'] != null)
                        $departments_to_send[$student['department']]++;
                }
            return $departments_to_send;
        } catch (ApiException $e) {
        }
    }

    protected function arrayToStringConverter($array)
    {
        $index = 0;
        $result = "[";
        foreach ($array as $value) {
            $result .= "[";
            $result .= ++$index . ", ";
            $result .= "'" . $value . "'";
            if ($index == sizeof($array))
                $result .= "]";
            else
                $result .= "], ";
        }
        $result .= "]";
        return $result;
    }

    public function test()
    {
        $token = Str::random(60);
        event(new TestEvent($token));
        return 'mail send';
    }
}
