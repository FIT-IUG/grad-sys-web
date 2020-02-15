<?php

namespace app\http\controllers\firebase;

use app\http\controllers\controller;
use illuminate\http\request;
use Kreait\Firebase\Exception\ApiException;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class FirebaseController extends controller
{
    public function index()
    {

        $serviceaccount = serviceaccount::fromjsonfile(__dir__ . '/fugg_firebasekey.json');
        $firebase = (new factory)
            ->withserviceaccount($serviceaccount)
            ->withdatabaseuri('https://fugg-system.firebaseio.com')
            ->create();

        $database = $firebase->getdatabase();
        try {
            $createpost = $database
                ->getreference('users')
                ->push([
                    'id' => 1,
                    'name' => 'osama'
                ]);
            $createpost = $database
                ->getreference('Admins')
                ->push([
                    'id' => 1,
                    'name' => 'osama'
                ]);
            $createpost = $database
                ->getreference('Teachers')
                ->push([
                    'id' => 1,
                    'name' => 'osama'
                ]);
            $createpost = $database
                ->getreference('Students')
                ->push([
                    'id' => 1,
                    'name' => 'osama'
                ]);
            $createpost = $database
                ->getreference('Groups')
                ->push([
                    'id' => 1,
                    'name' => 'osama'
                ]);
            $createpost = $database
                ->getreference('Projects')
                ->push([
                    'id' => 1,
                    'name' => 'osama'
                ]);
            $createpost = $database
                ->getreference('project_group')
                ->push([
                    'id' => 1,
                    'name' => 'osama'
                ]);
            $createpost = $database
                ->getreference('group_student')
                ->push([
                    'id' => 1,
                    'name' => 'osama'
                ]);
            $createpost = $database
                ->getreference('Courses')
                ->ush([
                    'name' => 'osama'
                ]);
        } catch (ApiException $e) {
        }

        echo '<pre>';
        try {
            print_r($createpost->getvalue());
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
        $createpost = $database->getreference('users')
            ->getvalue();
        return response()->json($createpost);
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
        $createpost = $database
            ->getreference('users/1')
            ->push([
                'name' => '' . $name
            ]);

        echo '<pre>';
        print_r($createpost->getvalue());
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
