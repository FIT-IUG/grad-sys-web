<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
<<<<<<< HEAD
=======
use Illuminate\Support\Arr;
>>>>>>> osama

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
<<<<<<< HEAD
=======

    public function getStudentsWithoutTeam()
    {

//        get students std from groups table, from all students (members and leaders)
        $groups = firestoreCollection('groups')->documents()->rows();
        $leadersStd = Arr::pluck($groups, 'leaderStudentStd');
        $members_std = Arr::pluck($groups, 'membersStd');
        $members_std = Arr::flatten($members_std);

        $registered_groups_std = Arr::collapse([$leadersStd, $members_std]);
        $students = firestoreCollection('users')->where('role','=','student')->documents()->rows();
        $students = Arr::pluck($students, 'user_id');

        return array_diff($students, $registered_groups_std);

    }
>>>>>>> osama
}
