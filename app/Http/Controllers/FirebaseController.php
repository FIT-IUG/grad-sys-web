<?php

namespace App\Http\Controllers;

use Kreait\Firebase\Exception\ApiException;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class FirebaseController extends Controller
{
    public $firebase;
    private $jsonLink;

    public function __construct()
    {
        $this->jsonLink = serviceaccount::fromjsonfile(__dir__ . '/firebaseKey.json');
        $this->firebase = (new factory)
            ->withserviceaccount($this->jsonLink)
            ->withdatabaseuri('https://fugg-system.firebaseio.com')
            ->createDatabase();
    }

    public function store()
    {
        try {
            firebaseCreateData()->getReference('users')->push([
                'email' => 'admin@example.com',
                'password' => 'admin123',
                'role' => 'admin',
                'remember_token' => ''
            ]);
            firebaseCreateData()->getReference('users')->push([
                'email' => 'teacher@example.com',
                'password' => 'teacher123',
                'role' => 'teacher',
                'remember_token' => ''
            ]);
            firebaseCreateData()->getReference('users')->push([
                'email' => 'student@example.com',
                'password' => 'student123',
                'role' => 'student',
                'remember_token' => ''
            ]);
//            firebaseCreateData()->getReference('roles/0')->set(['name' => 'admin']);
//            firebaseCreateData()->getReference('roles/1')->set(['name' => 'teacher']);
//            firebaseCreateData()->getReference('roles/2')->set(['name' => 'student']);
//            firebaseCreateData()->getReference('role_user')->push([
//                'user_key' => '',
//                'role_id' => ''
//            ]);
        } catch (ApiException $e) {
            return redirect()->back()->with('error', 'cant set any roles.');
        }
        return redirect()->back()->with('success', 'success to set roles.');
    }

    public function createUser()
    {
        $email = 'student@example.com';
        $password = 'student123';
        $user = firebaseCreateAuth()->createUserWithEmailAndPassword($email, $password);
        dd($user);
    }
}
