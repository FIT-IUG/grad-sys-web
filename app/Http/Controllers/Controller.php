<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Arr;
use Kreait\Firebase\Exception\ApiException;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function getStudentsWithoutTeam()
    {

//        get students std from groups table, from all students (members and leaders)
        try {
            $registered_groups_std = getStudentsStdInGroups();
            dd($registered_groups_std);
            $students = firebaseGetReference('users')->getValue();
            foreach ($students as $student) {

            }

            $students = firestoreCollection('users')->where('role', '=', 'student')->documents()->rows();
            $students = Arr::pluck($students, 'user_id');

            return array_diff($students, $registered_groups_std);

        } catch (ApiException $e) {
        }

    }
}
