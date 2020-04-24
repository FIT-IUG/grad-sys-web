<?php

use Illuminate\Support\Arr;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

//use \Faker\Generator as Faker;

function firebaseCreateData()
{
    $jsonLink = ServiceAccount::fromjsonfile(app_path('Http/Controllers/firebaseKey.json'));
    return (new Factory)
        ->withServiceAccount($jsonLink)
        ->createDatabase();
}

function firebaseCreateAuth()
{
    $jsonLink = ServiceAccount::fromjsonfile(app_path('Http/Controllers/firebaseKey.json'));
    return (new Factory)
        ->withServiceAccount($jsonLink)
        ->createAuth();
}

function hasRole($role)
{
//    $userRole = firebaseCreateData()->getReference('users/' . session()->get('uid') . '/role')->getValue();
    if (getRole() == $role)
        return true;
    return false;
}


function hasRoles($roles)
{
//    $userRole = firebaseCreateData()->getReference('users/' . session()->get('userId') . '/role')->getValue();
    foreach ($roles as $role)
        if (getRole() == $role)
            return true;
    return false;
}

function firestoreCollection($collection_name): Google\Cloud\Firestore\CollectionReference
{
//    dd(app('firebase.firestore')->database()->collection($collection_name));
    return app('firebase.firestore')->database()->collection($collection_name);
}

function collectionSize($collection_name)
{
    return app('firebase.firestore')->database()->collection($collection_name)->documents()->size();
}

//get last index for document to create new document
//function getDocumentIndex($collection_name)
//{
//
//    dd(firestoreCollection($collection_name)->newDocument()->set(['name' => 'hh']));
//    $index = \Illuminate\Support\Arr::last(firestoreCollection($collection_name)->documents()->rows());
//    $index = \Illuminate\Support\Arr::last(firestoreCollection($collection_name)->documents()->getIterator()->ksort());
//    dd(firestoreCollection($collection_name)->documents()->rows());
//    return $index->id() + 1;
//}

function getNotification($notification_key)
{
    try {
        $email = firestoreCollection('users')->document(session()->get('uid'))->snapshot()->data()['email'];
        $std = firestoreCollection('students')->where('email', '=', $email)->documents()->rows()[0]->data()['std'];
        return firestoreCollection('notifications')
            ->where('to', '=', $std)
            ->documents()->rows()[0]->data()[$notification_key];
    } catch (Exception $exception) {
        return null;
    }
}

function getRole()
{
    return firestoreCollection('users')->document(session()->get('uid'))->snapshot()->data()['role'];
}

function inGroup()
{

    $groups = firestoreCollection('groups')->documents()->rows();
    $leadersStd = Arr::pluck($groups, 'leaderStudentStd');
    $members_std = Arr::pluck($groups, 'membersStd');
    $members_std = Arr::flatten($members_std);
    $student_std_in_groups = Arr::collapse([$leadersStd, $members_std]);
    $registered = getStudentStd();
    foreach ($student_std_in_groups as $student_std_in_group) {
        if ($student_std_in_group == $registered) {
            return true;
        }
    }
    return false;
}

function isTeamLeader()
{
    $leaders = firestoreCollection('groups')->documents()->rows();
    $leaders = \Illuminate\Support\Arr::pluck($leaders, 'leaderStudentStd');
    foreach ($leaders as $leader)
        if ($leader == getStudentStd())
            return true;
    return false;
}

function getStudentStd()
{

    $email = firestoreCollection('users')->document(session()->get('uid'))->snapshot()->data()['email'];
//    dd($email);
//    dd(firestoreCollection('students')->where('email', '=', $email)->documents()->rows());
    return firestoreCollection('students')->where('email', '=', $email)->documents()->rows()[0]->data()['std'];
}

function firebaseAuth(): \Kreait\Firebase\Auth
{
    return app('firebase.auth');
}

function isTeacherHasNotification()
{
    $std = getStudentStd();
    $notification_for_student = firestoreCollection('notifications')->where('from', '=', $std)->documents()->rows();
    foreach ($notification_for_student as $snapshot)
        if ($snapshot['type'] == 'to_be_supervisor')
            return true;
    return false;
}

function isTeacherAccept()
{
    $std = getStudentStd();
    $notification_for_student = firestoreCollection('notifications')->where('from', '=', $std)->documents()->rows();
    foreach ($notification_for_student as $snapshot) {
        if ($snapshot['type'] == 'to_be_supervisor') {
            if ($snapshot['isAccept'] == 1) {
                return true;
            } elseif ($snapshot['isAccept'] == null)
                return null;
        }
    }
    return false;
}

function studentGenerator($number_of_students)
{
    $faker = Faker\Factory::create();
    $departments = ['تطوير البرمجيات', 'علم الحاسوب', 'نظم المعلومات', 'مالتيميديا', 'موبايل', 'تكنولوجيا المعلومات'];//'','','','',''
    for ($i = 0; $i < $number_of_students; $i++) {
        try {
            $id = firestoreCollection('students')->newDocument()->id();
            $email = 'student' . random_int(1, 10000) . '@example.com';
            $uid = firebaseAuth()->createUserWithEmailAndPassword($email, 'student123')->uid;

            firestoreCollection('students')
                ->document($id)
                ->create([
                    'name' => $faker->name,
                    'department' => Arr::random($departments, 1)[0],
                    'email' => $email,
                    'std' => '12016' . $faker->randomNumber(4),
                    'mobile_number' => $faker->phoneNumber,
                ]);

            firestoreCollection('users')
                ->document($uid)->create([
                    'email' => $email,
                    'role' => 'student',
                    'remember_token' => '',
                    'document_id' => $id
                ]);
        } catch (Exception $exception) {
            return 'مشكلة في صانع الطلبة';
        } catch (\Kreait\Firebase\Exception\AuthException $e) {
        } catch (\Kreait\Firebase\Exception\FirebaseException $e) {
        }

    }
}

function teacherGenerator($number_of_teachers)
{
    $faker = Faker\Factory::create();
    for ($i = 0; $i < $number_of_teachers; $i++) {
        try {
            $id = firestoreCollection('teachers')->newDocument()->id();
            $email = 'teacher' . random_int(1, 10000) . '@example.com';
            $uid = firebaseAuth()->createUserWithEmailAndPassword($email, 'teacher123')->uid;

            firestoreCollection('teachers')
                ->document($id)
                ->create([
                    'name' => $faker->name,
//                    'department' => Arr::random($departments, 1)[0],
                    'email' => $email,
//                    'std' => '12016' . $faker->randomNumber(4),
                    'mobile_number' => $faker->phoneNumber,
                ]);

            firestoreCollection('users')
                ->document($uid)->create([
                    'email' => $email,
                    'role' => 'teacher',
                    'remember_token' => '',
                    'document_id' => $id
                ]);
        } catch (Exception $exception) {
            return 'مشكلة في صانع المدرسين';
        } catch (\Kreait\Firebase\Exception\AuthException $e) {
        } catch (\Kreait\Firebase\Exception\FirebaseException $e) {
        }

    }
}

