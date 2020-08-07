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

function getUserId()
{
    try {
        return firebaseGetReference('users/' . session()->get('uid'))->getValue()['user_id'];
    } catch (\Kreait\Firebase\Exception\ApiException $e) {
        return redirect()->back()->with('error', 'حلصت مشكلة بالنظام.');
    }
}

function createUsers()
{
    try {
        $uid = firebaseAuth()->createUserWithEmailAndPassword('admin@example.com', 'admin123')->uid;
//        $uid = firebaseAuth()->verifyPassword('leader@example.com', 'admin123')->uid;

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
                if (isset($user['role']) && $user['role'] == $user_role)
                    Arr::set($selected_users, $key, $user);
        return $selected_users;
    } catch (\Kreait\Firebase\Exception\ApiException $e) {
        return redirect()->route('logout')->with('error', 'حدثت مشكلة في النظام.');
    }
}
