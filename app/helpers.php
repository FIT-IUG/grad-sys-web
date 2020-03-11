<?php

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

function firebaseCreateData()
{
    $jsonLink = serviceaccount::fromjsonfile(app_path('Http/Controllers/firebaseKey.json'));
    return (new Factory)
        ->withServiceAccount($jsonLink)
        ->createDatabase();
}

function firebaseCreateAuth()
{
    $jsonLink = serviceaccount::fromjsonfile(app_path('Http/Controllers/firebaseKey.json'));
    return (new Factory)
        ->withServiceAccount($jsonLink)
        ->createAuth();
}

function hasRole($role)
{
    $userRole = firebaseCreateData()->getReference('users/' . session()->get('userId') . '/role')->getValue();
    if ($userRole == $role)
        return true;
    return false;
}


function hasRoles($roles)
{
    $userRole = firebaseCreateData()->getReference('users/' . session()->get('userId') . '/role')->getValue();
    foreach ($roles as $role)
        if ($userRole == $role)
            return true;
    return false;
}
