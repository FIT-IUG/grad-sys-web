<?php

namespace app\http\controllers\firebase;

use App\Http\Controllers\Firebase\MainController;
use illuminate\http\request;
use Kreait\Firebase\Database;
use Kreait\Firebase\Exception\ApiException;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class FirebaseController extends MainController
{
    public function index()
    {

        $database = $this->firebase;
//        $counterRef = $database->getReference('counter');
//        $key = $database->getReference('users')->push()->getKey();
//        dd($key);
        try {
            $createFirebase = $database
                ->getreference('users/1')
                ->set([
                    'id' => 1,
                    'name' => 'user',
//                    'std' => 120161616,
                    'email' => 'student@example.com',
//                    'department' => 'Software development',
                    'password' => 'password',
                    'created_at' => Database::SERVER_TIMESTAMP,
                    'updated_at' => '',
                    'email_verified_at' => '',
                ]);
            $createFirebase = $database
                ->getreference('admins')
                ->set([
                    'id' => 1,
                    'name' => 'admin',
                    'email' => 'admin@example.com',
                    'password' => 'password'
                ]);
            $createFirebase = $database
                ->getreference('teachers')
                ->set([
                    'id' => 1,
                    'name' => 'teacher',
                    'email' => 'teacher@example.com',
                    'password' => 'password',
                    'mobile_number' => '0599999999'
                ]);
            $createFirebase = $database
                ->getreference('students')
                ->set([
                    'id' => 1,
                    'name' => 'student',
                    'std' => 120161616,
                    'email' => 'student@example.com',
                    'mobile_number' => '0599999999',
                    'department' => 'Software development',
                    'password' => 'password',
                ]);
            $createFirebase = $database
                ->getreference('groups')
                ->set([
                    'id' => 1,
                    'course_code' => 'this is course code',
                    'student_id' => 1,//team leader
                    'teacher_id' => 1,
                ]);
            $createFirebase = $database
                ->getreference('projects')
                ->set([
                    'id' => 1,
                    'title' => 'this is project title',
                    'description' => 'this is project description',
                    'group_id' => 1
                ]);
            $createFirebase = $database
                ->getreference('group_student')
                ->set([
                    'id' => 1,
                    'group_id' => 1,
                    'student_id' => 1
                ]);
        } catch (ApiException $e) {
        }

        echo '<pre>';
        try {
            print_r($createFirebase->getvalue());
        } catch (ApiException $e) {
        }
        echo '</pre>';

    }

    public function getdata()
    {

        $serviceaccount = serviceaccount::fromjsonfile(__dir__ . '/fugg_firebasekey.json');
        $firebase = (new factory)
            ->withserviceaccount($serviceaccount)
            ->withdatabaseuri('https://fugg-system.firebaseio.com')
            ->create();
        $database = $firebase->getdatabase();
        $createFirebase = $database->getreference('users')
            ->getvalue();
        return response()->json($createFirebase);
    }

    public function test()
    {
        $database = (new Factory())->createDatabase();
        $reference = $database->getReference('Admins/name/admin');
        $snapshot = $reference->getSnapshot();
        $value = $snapshot->getValue();
        dd($value);
    }

    public function create()
    {
        return view('firebase');
    }

    public function store(Request $request)
    {

        $name = $request['name'];
        $serviceaccount = serviceaccount::fromjsonfile(__dir__ . '/fugg_firebasekey.json');
        $firebase = (new factory)
            ->withserviceaccount($serviceaccount)
            ->withdatabaseuri('https://fugg-system.firebaseio.com')
            ->create();

        $database = $firebase->getdatabase();
        $createFirebase = $database
            ->getreference('users/1')
            ->set([
                'name' => '' . $name
            ]);

        echo '<pre>';
        print_r($createFirebase->getvalue());
        echo '</pre>';
        return $name;
    }

    public function firebaseReady()
    {
        $serviceaccount = serviceaccount::fromjsonfile(__dir__ . '/fugg_firebasekey.json');
        $firebase = (new factory)
            ->withserviceaccount($serviceaccount)
            ->withdatabaseuri('https://fugg-system.firebaseio.com')
            ->create();

        $database = $firebase->getdatabase();
        return $database;
    }
}
