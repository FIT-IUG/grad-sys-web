<?php

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

function firebaseCreateData(){
    $jsonLink = serviceaccount::fromjsonfile(app_path('Http/Controllers/firebaseKey.json'));
    return (new Factory)
        ->withServiceAccount($jsonLink)
        ->createDatabase();
}
function firebaseCreateAuth(){
    $jsonLink = serviceaccount::fromjsonfile(app_path('Http/Controllers/firebaseKey.json'));
    return (new Factory)
        ->withServiceAccount($jsonLink)
        ->createAuth();
}
