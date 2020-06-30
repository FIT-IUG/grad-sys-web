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

function getNotification()
{
    try {
//        $user = firebaseGetReference('users/' . session()->get('uid'))->getValue();

        $user_id = getUserId();

        $notifications = firebaseGetReference('notifications')->getValue();
        $user_notifications = [];
        $index = 0;
        foreach ($notifications as $notification) {
            if ($notification['to'] == $user_id) {
                Arr::set($user_notifications, $index++, $notification);
            }
        }

        return $user_notifications;

    } catch (Exception $exception) {
        return null;
    }
}

function getRole()
{
    try {
        return firebaseGetReference('users/' . session()->get('uid'))->getValue()['role'];
    } catch (\Kreait\Firebase\Exception\ApiException $e) {
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
        return null;
    } catch (\Kreait\Firebase\Exception\ApiException $e) {
        return redirect()->back()->with('error', 'حدثت مشكلة.');
    }
}

function getStudentsStdWithoutGroup()
{
    try {
        $students_std = [];
        $index = 0;
        $studentsStdInGroup = getStudentsStdInGroups();
        $students = getUserByRole('student');
        $student_gender = Str::substr(getUserId(), 0, 1);
        $logged_student_std = getUserId();

        foreach ($students as $student) {
            if (Str::startsWith($student['user_id'], $student_gender)) {
                if ($student['user_id'] == $logged_student_std)
                    continue;
                else
                    Arr::set($students_std, $index++, $student['user_id'] . '');
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
        $student_in_group = getStudentsStdInGroups();

        if ($student_in_group == null)
            return false;

        foreach ($student_in_group as $student_std_in_group) {
            if ($student_std_in_group == $user_id) {
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
        $leaders = \Illuminate\Support\Arr::pluck($leaders, 'leaderStudentStd');
        $user_id = firebaseGetReference('users/' . session()->get('uid'))->getValue()['user_id'];
        foreach ($leaders as $leader)
            if ($leader == $user_id)
                return true;
        return false;
    } catch (\Kreait\Firebase\Exception\ApiException $e) {
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
    }catch (ErrorException $exception){
        return route('logout');
//        return redirect()->back()->with('error','هنالك مشكلة في النظام.');
    }

}

function getSupervisorNotificationStatus()
{

    try {
        $std = getUserId();
        $notifications =  firebaseGetReference('notifications')->getValue();
        foreach ($notifications as $notification) {
            if ($notification['from'] == $std && $notification['type'] == 'to_be_supervisor') {
               return $notification['status'];
//                if ($notification['status'] == 1)
//                    return true;
//                elseif ($notification['status'] == 0)
//                    return false;
            }
        }
        return false;
    } catch (\Kreait\Firebase\Exception\ApiException $e) {
        return redirect()->back()->with('error', 'حصلت مشكلة');
    }catch (ErrorException $exception){
        return redirect()->back()->with('error','هنالك مشكلة في النظام.');
    }

}

function numberOfTeamedStudents()
{
    $groups = firestoreCollection('groups')->documents()->rows();
    $leadersStd = Arr::pluck($groups, 'leaderStudentStd');
    $members_std = Arr::pluck($groups, 'membersStd');
    $members_std = Arr::flatten($members_std);

    $registered_groups_std = Arr::collapse([$leadersStd, $members_std]);
    return sizeof($registered_groups_std);
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
        $index = 0;
        $selected_users = [];
        $users = firebaseGetReference('users')->getValue();
        foreach ($users as $user) {
            if ($user['role'] == $user_role) {
                Arr::set($selected_users, $index++, $user);
            }
        }
        return $selected_users;
    } catch (\Kreait\Firebase\Exception\ApiException $e) {
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
            if ($notification['from'] == $std && $notification['type'] == 'join_group') {
                if ($notification['status'] == 1)
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
    $notifications = firebaseGetReference('notifications')->getValue();
    $std = getUserId();
    if ($notifications != null) {
        foreach ($notifications as $notification) {
            if ($notification['to'] == $std) {
                return $notification['status'];
            }
        }
    }

}
