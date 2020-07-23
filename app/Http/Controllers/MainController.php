<?php

namespace App\Http\Controllers;


use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
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
//                       to be supervisor notification type send for teacher and admin
                        if ($notification['type'] == 'to_be_supervisor') {
//                            dd($notification);
                            $groups = firebaseGetReference('groups')->getValue();
//                      get project initial title
                            foreach ($groups as $group) {
                                if ($group['leaderStudentStd'] == $notification['from']) {
                                    $teacher_notification = Arr::add($notification, 'initialProjectTitle', $group['initialProjectTitle']);
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

    public function create()
    {

        $email = 'admin2@example.com';
        $password = 'admin123';
        $role = 'admin';
        $user_id = '111111111';
        $mobile_number = '0597412325';
        $department = 'FIT';

        try {
            $uid = firebaseAuth()->createUserWithEmailAndPassword($email, $password)->uid;
            firebaseGetReference('users/' . $uid)->set([
                'email' => $email,
                'name' => 'student' . $user_id,
                'role' => $role,
                'mobile_number' => $mobile_number,
                'user_id' => $user_id,
                'department' => $department
            ]);
            return 'user created successfully';
        } catch (AuthException $e) {
        } catch (FirebaseException $e) {
        }

    }

    protected function groupsDataForTeacher($id)
    {
        $teacher_id = $id;
        if ($id == null)
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


    protected function getGroupMembersData($members_std, $leader_id = 0)
    {
        $group_members_data = [];
        $students = getUserByRole('student');
        $index = 0;

        if ($leader_id == 0) {
            foreach ($students as $student) {
                foreach ($members_std as $std)
                    if ($student['user_id'] == $std) {
                        $student = Arr::except($student, ['remember_token', 'role']);
                        Arr::set($group_members_data, $index++, $student);
                        break;
                    }
                if (sizeof($members_std) == $index)
                    break;
            }
            return $group_members_data;
        } else {
            $leader_data = [];
            foreach ($students as $student) {
                if ($student['user_id'] == $leader_id) {
                    $leader_data = Arr::except($student, ['remember_token', 'role']);
                }
                foreach ($members_std as $std) {
                    if (isset($student['user_id']) && $student['user_id'] == $std) {
                        $student = Arr::except($student, ['remember_token', 'role']);
                        Arr::set($group_members_data, $index++, $student);
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

    protected function getTeacherData($teacher_id)
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

    public function replayToBeSupervisorRequest(Request $request)
    {

        try {
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
                return redirect()->route(getRole() . '.index')->with('success', 'تم قبول الطلب بنجاح.');
            } elseif ($reply == 'reject') {
                firebaseGetReference('notifications/' . $key)->update(['status' => 'reject']);
                return redirect()->back()->with('success', 'تم رفض الطلب بنجاح.');
            } else
                return redirect()->back()->with('error', 'حصلت مشكلة في الطلب.');
        } catch (ApiException $e) {
            return redirect()->back()->with('error', 'حصلت مشكلة في الطلب.');
        }
    }


}
