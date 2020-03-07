<?php

namespace App\Http\Controllers;

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class FirebaseController extends Controller
{
    public $firebase;
    private $jsonLink;

    public function __construct(){
        $this->jsonLink = serviceaccount::fromjsonfile(__dir__ . '/firebaseKey.json');
        $this->firebase = (new factory)
            ->withserviceaccount($this->jsonLink)
            ->withdatabaseuri('https://fugg-system.firebaseio.com')
            ->createDatabase();
    }

}
