<?php

namespace App\Http\Controllers\Firebase;

use App\Http\Controllers\Controller;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class MainController extends Controller
{
    private $jsonLink;
    public $database;

    public function __construct()
    {
        $this->jsonLink = serviceaccount::fromjsonfile(__dir__ . '/firebaseKey.json');
        $this->database = (new factory)
            ->withserviceaccount($this->jsonLink)
            ->withdatabaseuri('https://fugg-system.firebaseio.com')
            ->createDatabase();
    }

}
