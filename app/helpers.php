<?php

use Illuminate\Support\Arr;
use Facade\Ignition\Exceptions\ViewException;

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

function firebaseGetReference($collection_name): Kreait\Firebase\Database\Reference
{
    return app('firebase.database')->getReference($collection_name);
}

function getLastIdForDocument($document_name)
{
    return last(app('firebase.database')->getReference($document_name)->getChildKeys());
}

function getNotification($notification_key)
{
    try {
        $user = firebaseGetReference('users/' . session()->get('uid'))->getValue();

//            ->document(session()->get('uid'))->snapshot()->get('email');
//        $email = firestoreCollection('users')->document(session()->get('uid'))->snapshot()->get('email');
        $user_id = $user['user_id'];
//        $std = firestoreCollection('users')->where('role', '=', 'student')
//            ->where('email', '=', $email)->documents()->rows()[0]->data()['std'];

        $notifications = firebaseGetReference('notifications')->getValue();
        $user_notifications = [];
        $index = 0;
        foreach ($notifications as $notification) {
            if ($notification['to'] == $user_id) {
                Arr::set($user_notifications, $index++, $notification);
            }
        }
//        return firestoreCollection('notifications')
//            ->where('to', '=', $std)
//            ->documents()->rows()[0]->data()[$notification_key];
    } catch (Exception $exception) {
        return null;
    }
}

function getRole()
{
    return firestoreCollection('users')->document(session()->get('uid'))->snapshot()->get('role');
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
        foreach ($students as $student) {
            Arr::set($students_std, $index++, $student['user_id'] . '');
        }
        dd($students_std);
        dd(array_diff());
    } catch (\Kreait\Firebase\Exception\ApiException $e) {
        return redirect()->back()->with('error', 'حصلت مشكلة في جلب الطلبة');
    }
}

function inGroup()
{
    try {
        $user_id = firebaseGetReference('users/' . session()->get('uid'))->getValue()['user_id'];
        if (getStudentsStdInGroups() != null) {
            foreach (getStudentsStdInGroups() as $student_std_in_group) {
                if ($student_std_in_group == $user_id) {
                    return true;
                }
            }
            return false;
        }
    } catch (\Kreait\Firebase\Exception\ApiException $e) {
    }
}

function isGroupLeader()
{
    $leaders = firebaseGetReference('groups')->getValue();
    $leaders = \Illuminate\Support\Arr::pluck($leaders, 'leaderStudentStd');
    $user_id = firebaseGetReference('users/' . session()->get('uid'))->getValue()['user_id'];
    foreach ($leaders as $leader)
        if ($leader == $user_id)
            return true;
    return false;
}

function getUserId()
{
    try {
        return firebaseGetReference('users/' . session()->get('uid'))->getValue()['user_id'];
    } catch (\Kreait\Firebase\Exception\ApiException $e) {
    }
//    return firestoreCollection('users')->document(session()->get('uid'))->snapshot()->get('user_id');
}

function firebaseAuth(): \Kreait\Firebase\Auth
{
    return app('firebase.auth');
}

function isTeacherHasNotification()
{
    $user_id = firebaseGetReference('users/' . session()->get('uid'))->getValue()['user_id'];
    $notifications = firebaseGetReference('notifications')->getValue();
    foreach ($notifications as $notification)
        if ($notification['type'] == 'to_be_supervisor' && $notification['from'] == $user_id)
            return true;
    return false;
}

function isTeacherAccept()
{
    $std = firebaseGetReference('users/' . session()->get('uid'))->getValue()['user_id'];
    $notifications = firebaseGetReference('notifications')->getValue();
    foreach ($notifications as $notification) {
        if ($notification['from'] == $std && $notification['type'] == 'to_be_supervisor') {
            if ($notification['isAccept'] == 1)
                return true;
            elseif ($notification['isAccept'] == 0)
                return null;
        }
    }
    return false;
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
