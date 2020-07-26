<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\MainController;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Kreait\Firebase\Exception\ApiException;

class GroupController extends MainController
{

    public function __construct()
    {
        $this->middleware('checkRole');
    }

    public function index()
    {
        try {

            $groups = firebaseGetReference('groups')->getValue();
            $all_groups = [];

            if ($groups != null)
                foreach ($groups as $key => $group)
                    Arr::set($all_groups, $key, $this->getAllGroupInfoForTeacher($key));

            return view('admin.group.index')->with([
                'groups' => $all_groups
            ]);

        } catch (ApiException $e) {
            return redirect()->back()->with('error', 'حصلت مشكلة في جلب بيانات المجموعات.');
        }

    }

    public function edit($group_key)
    {

        dd($this->getAllGroupInfoForTeacher($group_key));
        return view('admin.group.edit', $this->getAllGroupInfoForTeacher($group_key));
    }

    public function update(Request $request, $group_key)
    {
        try {
            $student_id = $request->get('student_id');
            $student = firebaseGetReference('users/' . $student_id);
            firebaseGetReference('groups/' . $group_key . '/membersStd/' . $student_id)
                ->set($student->getChild('user_id')->getValue());
            firebaseGetReference('androidStudentsStdInGroups')->push($student->getChild('user_id'));

            return redirect()->route('admin.group.edit', ['group_key' => $group_key])->with('success', 'تم اضافة الطالب ' . $student->getChild('name')->getValue() . ' إلى المجموعة بنجاح.');
        } catch (ApiException $e) {
        }
    }
//
//    private function getAllGroupInfoForTeacher($group_key)
//    {
//        try {
//            $group = firebaseGetReference('groups/' . $group_key)->getValue();
//
//            if (is_array($group['membersStd']))
//                $group_members_data = $this->getGroupMembersData($group['membersStd'], $group['leaderStudentStd']);
//            else
//                $group_members_data = $this->getGroupMembersData([$group['membersStd']], $group['leaderStudentStd']);
//
//            if (isset($group['teacher']))
//                $teacher_data = $this->getTeacherData($group['teacher']);
//            else
//                $teacher_data = null;
//
//            if (isset($group['initialProjectTitle'])) {
//                Arr::set($project_data, 0, [
//                    'initialProjectTitle' => $group['initialProjectTitle'],
//                    'graduateInFirstSemester' => $group['graduateInFirstSemester'],
//                    'tags' => $group['tags']
//                ]);
//                $project_data = $project_data[0];
//            } else
//                $project_data = null;
//
//            return [
//                'group_leader_data' => $group_members_data['leader_data'],
//                'group_members_data' => $group_members_data['members_data'],
//                'teacher_data' => $teacher_data,
//                'project_data' => $project_data,
//                'students' => getStudentsStdWithoutGroup(),
//                'group_key' => $group_key
//            ];
//        } catch (ApiException $e) {
//            return redirect()->back()->with('error', 'حصلت مشكلة في جلب بيانات المجموعات.');
//        }
//
//    }

}
