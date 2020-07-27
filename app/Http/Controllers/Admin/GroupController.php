<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\MainController;
use App\Http\Requests\ChangeTeacherForGroupRequest;
use App\Http\Requests\RegeterStudentInGroupRequest;
use Illuminate\Support\Arr;
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
        $teacher_id = firebaseGetReference('groups/' . $group_key)->getChild('teacher')->getValue();
        $teachers = $this->getTeachersCanBeSupervisor($teacher_id);

        return view('admin.group.edit', $this->getAllGroupInfoForTeacher($group_key))->with([
            'teachers' => $teachers
        ]);
    }

    public function update(RegeterStudentInGroupRequest $student, $group_key)
    {
        try {
            $student_id = $student->get('student_id');
            $student = firebaseGetReference('users/' . $student_id);
            firebaseGetReference('groups/' . $group_key . '/membersStd/' . $student_id)
                ->set($student->getChild('user_id')->getValue());
            firebaseGetReference('androidStudentsStdInGroups')->push($student->getChild('user_id'));

            return redirect()->route('admin.group.edit', ['group_key' => $group_key])->with('success', 'تم اضافة الطالب ' . $student->getChild('name')->getValue() . ' إلى المجموعة بنجاح.');
        } catch (ApiException $e) {
        }
    }

    public function updateTeacher(ChangeTeacherForGroupRequest $teacher, $group_key)
    {
        try {
            firebaseGetReference('groups/' . $group_key)->update(['teacher' => $teacher->get('teacher_id')]);
            return redirect()->route('admin.group.edit', ['group_key' => $group_key]);
        } catch (ApiException $e) {
        }
    }

}
