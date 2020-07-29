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

function getRole()
{
    try {
        return firebaseGetReference('users/' . session()->get('uid'))->getValue()['role'];
    } catch (\Kreait\Firebase\Exception\ApiException $e) {
        return redirect()->back()->with('error', 'حلصت مشكلة بالنظام.');
    } catch (ErrorException $exception) {
        return redirect()->back()->with('error', 'حدثت مشكلة بالنظام.');
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
        return redirect()->back()->with('error', 'حدثت مشكلة أثناء جلب الطلبة');
    }
}

function inGroup()
{
    try {
        $user_id = getUserId();
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

<<<<<<< HEAD
=======
function isGroupLeader()
{

    try {
        $groups = firebaseGetReference('groups')->getValue();
//        dd($groups);
        $leaders = Arr::pluck($groups, 'leaderStudentStd', key($groups));
        dd($leaders);
        $user_id = getUserId();
//        firebaseGetReference('users/' . session()->get('uid'))->getValue()['user_id'];
        foreach ($leaders as $leader)
            if ($leader == $user_id)
                return true;
        return false;
    } catch (\Kreait\Firebase\Exception\ApiException $e) {
        return redirect()->back()->with('error', 'حدثت مشكلة في النظام.');
    }

}

>>>>>>> ee3a44873b75501166e5074f6a3a16f38bae8eef
function getUserId()
{
    try {
        return firebaseGetReference('users/' . session()->get('uid'))->getValue()['user_id'];
    } catch (\Kreait\Firebase\Exception\ApiException $e) {
        return redirect()->back()->with('error', 'حلصت مشكلة بالنظام.');
    }
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
        return redirect()->back()->with('error', 'حدثت مشكلة');
    } catch (ErrorException $exception) {
        return route('logout');
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
        return redirect()->back()->with('error', 'حدثت مشكلة في النظام.');
    }

}

function createUsers()
{
    try {
        $uid = firebaseAuth()->createUserWithEmailAndPassword('admin@example.com', 'admin123')->uid;
//        $uid = firebaseAuth()->verifyPassword('admin@example.com', 'admin123')->uid;

        firebaseGetReference('users/' . $uid)->set([
            'email' => 'admin@example.com',
            'name' => 'admin1',
            'role' => 'admin',
            'user_id' => '1111111111',
            'mobile_number' => '1111111111',
            'department' => 'FIT'
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
        if ($users != null)
            foreach ($users as $key => $user)
                if ($user['role'] == $user_role)
                    Arr::set($selected_users, $key, $user);
        return $selected_users;
    } catch (\Kreait\Firebase\Exception\ApiException $e) {
        return redirect()->route('logout')->with('error', 'حدثت مشكلة في النظام.');
    }
}

//This function check if admin members is accept a min number of join requests by leader id
function isMinMembersAccept()
{

    try {
        $notifications = firebaseGetReference('notifications')->getValue();
        $min_group_members = firebaseGetReference('settings/min_group_members')->getValue();
        $accept_count = 0;
        $std = getUserId();
        if ($notifications != null)
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
        return redirect()->route('home')->with('error', 'حدثت مشكلة في النظام.');
    }

}
