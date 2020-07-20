<?php

use Illuminate\Support\Arr;
use Facade\Ignition\Exceptions\ViewException;
use Illuminate\Support\Str;

function firebaseAuth(): \Kreait\Firebase\Auth
{
    return app('firebase.auth');
}

function firebaseGetReference($collection_name): Kreait\Firebase\Database\Reference
{
    return app('firebase.database')->getReference($collection_name);
}

function hasRole($role)
{
    try {
        $uid = session()->get('uid');
        $user_role = firebaseGetReference('users/' . $uid)->getValue()['role'];
        if ($user_role == $role)
            return true;
        return false;
    } catch (ViewException $exception) {
        return 'exception from hasRole function';
    }

}

function getNotifications()
{
//    try {
//
//        $notifications = firebaseGetReference('notifications')->getValue();
//        $user_notifications = [];
//        $user_id = getUserId();
//        $teacher_notification = [];
//
//        if ($notifications != null) {
//            foreach ($notifications as $key => $notification) {
//                if ($notification['to'] == $user_id && ($notification['status'] == 'wait' xor $notification['status'] == 'readOnce')) {
////               this type of notification to teacher and need with normal data in notification a project initial title
//                    if ($notification['type'] == 'to_be_supervisor') {
//                        $groups = firebaseGetReference('groups')->getValue();
////                      get project initial title
//                        foreach ($groups as $group)
//                            if ($group['leaderStudentStd'] == $notification['from']) {
//                                $teacher_notification = Arr::collapse([
//                                    $notification, ['initial_title' => $group['project_initial_title']]
//                                ]);
//                                break;
//                            }
//                        Arr::set($user_notifications, $key, $teacher_notification);
//                    } else
//                        Arr::set($user_notifications, $key, $notification);
//                    if ($notification['status'] == 'readOnce')
//                        firebaseGetReference('notifications/' . $key)->update(['status' => 'read']);
//                }
//            }
//        }
//
//        return $user_notifications;
//
//    } catch (Exception $exception) {
//        return null;
//    } catch (\Kreait\Firebase\Exception\ApiException $e) {
//    }
}

function getRole()
{
    try {
        return firebaseGetReference('users/' . session()->get('uid'))->getValue()['role'];
    } catch (\Kreait\Firebase\Exception\ApiException $e) {
    } catch (ErrorException $exception) {
        return redirect()->back()->with('error', 'حلصت مشكلة بالنظام.');
    }
}

function getStudentsStdInGroups()
{
    try {
        $groups = firebaseGetReference('groups')->getValue();
        if ($groups != null) {
            $leadersStd = Arr::pluck($groups, 'leaderStudentStd');
            $members_std = Arr::pluck($groups, 'membersStd');
            $members_std = Arr::flatten($members_std);
            return Arr::collapse([$leadersStd, $members_std]);
        }
        return [];
    } catch (\Kreait\Firebase\Exception\ApiException $e) {
        return redirect()->back()->with('error', 'حدثت مشكلة.');
    }
}

function getStudentsStdWithoutGroup()
{
    try {
        $students_std = [];
        $studentsStdInGroup = getStudentsStdInGroups();
        $students = getUserByRole('student');
        $student_gender = Str::substr(getUserId(), 0, 1);
        $logged_student_std = getUserId();

        foreach ($students as $key => $student) {
            if (Str::startsWith($student['user_id'], $student_gender)) {
                if ($student['user_id'] == $logged_student_std)
                    continue;
                else
                    Arr::set($students_std, $key, $student['user_id'] . '');
            }
        }

        if ($studentsStdInGroup == null)
            return $students_std;

        return array_diff($students_std, $studentsStdInGroup);
    } catch (\Kreait\Firebase\Exception\ApiException $e) {
        return redirect()->back()->with('error', 'حصلت مشكلة في جلب الطلبة');
    }
}

function inGroup()
{
    try {
        $user_id = firebaseGetReference('users/' . session()->get('uid'))->getValue()['user_id'];
        $students_std_in_group = array_filter(getStudentsStdInGroups());

        if ($students_std_in_group == null)
            return false;

        foreach ($students_std_in_group as $std) {
            if ($std == $user_id) {
                return true;
            }
        }
        return false;

    } catch (\Kreait\Firebase\Exception\ApiException $e) {
    }
}

function isGroupLeader()
{

    try {
        $leaders = firebaseGetReference('groups')->getValue();
        $leaders = Arr::pluck($leaders, 'leaderStudentStd');
        $user_id = firebaseGetReference('users/' . session()->get('uid'))->getValue()['user_id'];
        foreach ($leaders as $leader)
            if ($leader == $user_id)
                return true;
        return false;
    } catch (\Kreait\Firebase\Exception\ApiException $e) {
        return redirect()->back()->with('error', 'حصلت مشكلة في النظام.');
    }

}

function getUserId()
{
    try {
        return firebaseGetReference('users/' . session()->get('uid'))->getValue()['user_id'];
    } catch (\Kreait\Firebase\Exception\ApiException $e) {
    }
//    return firestoreCollection('users')->document(session()->get('uid'))->snapshot()->get('user_id');
}

function isTeacherHasNotification()
{
    try {
        $user_id = firebaseGetReference('users/' . session()->get('uid'))->getValue()['user_id'];
        $notifications = firebaseGetReference('notifications')->getValue();
        foreach ($notifications as $notification)
            if ($notification['type'] == 'to_be_supervisor' && $notification['from'] == $user_id)
                return true;
        return false;
    } catch (\Kreait\Firebase\Exception\ApiException $e) {
        return redirect()->back()->with('error', 'حصلت مشكلة');
    } catch (ErrorException $exception) {
        return route('logout');
//        return redirect()->back()->with('error','هنالك مشكلة في النظام.');
    }

}

function getSupervisorStatus()
{

    try {
        $std = getUserId();
        $notifications = firebaseGetReference('notifications')->getValue();
        foreach ($notifications as $notification)
            if ($notification['from'] == $std && $notification['type'] == 'to_be_supervisor')
                return $notification['status'];

        return false;
    } catch (\Kreait\Firebase\Exception\ApiException $e) {
        return redirect()->back()->with('error', 'حصلت مشكلة');
    } catch (ErrorException $exception) {
        return redirect()->back()->with('error', 'هنالك مشكلة في النظام.');
    }

}

function createUsers()
{
    try {
//        $uid = firebaseAuth()->createUserWithEmailAndPassword('admin@example.com', 'admin123')->uid;
        $uid = firebaseAuth()->verifyPassword('admin@example.com', 'admin123')->uid;

        firebaseGetReference('users/' . $uid)->set([
            'email' => 'admin@example.com',
            'name' => 'admin1',
            'role' => 'admin',
            'user_id' => '1231231231'
        ]);

//        $uid = firebaseAuth()->createUserWithEmailAndPassword('teacher@example.com', 'teacher123')->uid;
        $uid = firebaseAuth()->verifyPassword('teacher@example.com', 'teacher123')->uid;

        firebaseGetReference('users/' . $uid)->set([
            'email' => 'teacher@example.com',
            'name' => 'teacher1',
            'role' => 'teacher',
            'user_id' => '1231231232'
        ]);

//        $uid = firebaseAuth()->createUserWithEmailAndPassword('student@example.com', 'student123')->uid;
        $uid = firebaseAuth()->verifyPassword('student@example.com', 'student123')->uid;

        firebaseGetReference('users/' . $uid)->set([
            'email' => 'student@example.com',
            'name' => 'student1',
            'role' => 'student',
            'user_id' => '1231231233'
        ]);
    } catch (\Kreait\Firebase\Exception\AuthException $e) {
    } catch (\Kreait\Firebase\Exception\FirebaseException $e) {
    }

}

function createUser($email, $password, $user_id)
{
    try {
        $uid = firebaseAuth()->createUserWithEmailAndPassword($email, $password)->uid;
        $uid = firebaseAuth()->verifyPassword($email, $password)->uid;

        firebaseGetReference('users/' . $uid)->set([
            'email' => $email,
            'name' => 'student',
            'role' => 'student',
            'user_id' => $user_id
        ]);
    } catch (\Kreait\Firebase\Exception\AuthException $e) {
    } catch (\Kreait\Firebase\Exception\FirebaseException $e) {
    }

}

function getUserByRole($user_role)
{
    try {
        $selected_users = [];
        $users = firebaseGetReference('users')->getValue();
        foreach ($users as $key => $user)
            if ($user['role'] == $user_role)
                Arr::set($selected_users, $key, $user);
        return $selected_users;
    } catch (\Kreait\Firebase\Exception\ApiException $e) {
        return redirect()->route('logout')->with('error', 'حصلت مشكلة في النظام.');
    }
}

//This function check if group members is accept a min number of join requests by leader id
function isMinMembersAccept()
{

    try {
        $notifications = firebaseGetReference('notifications')->getValue();
        $min_group_members = firebaseGetReference('settings/min_group_members')->getValue();
        $accept_count = 0;
        $std = getUserId();
        foreach ($notifications as $notification) {
            if ($notification['from'] == $std
                && $notification['type'] == 'join_group'
                && $notification['status'] == 'accept') {
                $accept_count++;
            }
        }
        if ($accept_count >= $min_group_members)
            return true;
        return false;
    } catch (\Kreait\Firebase\Exception\ApiException $e) {
    }

}

function isMemberHasNotification()
{
    try {
        $notifications = firebaseGetReference('notifications')->getValue();
        $std = getUserId();

        if ($notifications != null)
            foreach ($notifications as $notification)
                if ($notification['to'] == $std) {
                    if ($notification['status'] == 'accept')
                        return $notification['status'];
                    elseif ($notification['status'] == 'reject')
                        continue;
                }

        return null;
    } catch (\Kreait\Firebase\Exception\ApiException $e) {
        return redirect()->route('home')->with('error', 'حصلت مشكلة في النظام.');
    }

}
