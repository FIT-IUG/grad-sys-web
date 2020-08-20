<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\MainController;
use App\Http\Requests\ChangeTeacherForGroupRequest;
use App\Http\Requests\RegeterStudentInGroupRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
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
            return redirect()->back()->with('error', 'حدثت مشكلة أثناء جلب بيانات المجموعات.');
        }

    }

    public function edit($group_key)
    {
        $teacher_id = firebaseGetReference('groups/' . $group_key)->getChild('teacher')->getValue();
        $teachers = $this->getTeachersCanBeSupervisor($teacher_id);
        $tags = firebaseGetReference('tags')->getValue();
        $leader_std = firebaseGetReference('groups/' . $group_key . '/leaderStudentStd')->getValue();
        $students = $this->getStudentsNotInGroup($leader_std);
        foreach ($tags as $tag_key => $tag) {
            if (strtolower($tag) == 'fit') {
                Arr::forget($tags, $tag_key);
                break;
            }
        }
        return view('admin.group.edit', $this->getAllGroupInfoForTeacher($group_key))->with([
            'teachers' => $teachers,
            'tags' => $tags,
            'students' => $students
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

            return redirect()->route('admin.group.edit', ['group_key' => $group_key])->with('success', 'تم إضافة الطالب ' . $student->getChild('name')->getValue() . ' إلى المجموعة بنجاح.');
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

    public function updateGroupData(Request $request, $group_key)
    {
        try {
            $tags = $request->get('tags');
            $updated_tags = [];
            foreach ($tags as $tag) {
                $key = Str::random(20);
                Arr::set($updated_tags, $key, $tag);
            }
            firebaseGetReference('groups/' . $group_key)->update([
                'initialProjectTitle' => $request->get('projectTitle'),
                'tags' => $updated_tags
            ]);
            return redirect()
                ->route('admin.group.edit', ['group_key' => $group_key])
                ->with('success', 'تم تعديل البيانات بنجاح.');
        } catch (ApiException $e) {
        }
    }

    public function updateLeader($group_key)
    {
        try {
            firebaseGetReference('groups/' . $group_key)->update(['leaderStudentStd' => '-']);
        } catch (ApiException $e) {
        }
    }

    public function destroyMember($group_key, $student_key)
    {
        try {
            firebaseGetReference('groups/' . $group_key . '/membersStd/' . $student_key)->remove();
            return redirect()
                ->route('admin.group.edit', ['group_key' => $group_key])
                ->with('success', 'تم حذف العضو.');
        } catch (ApiException $e) {
        }
    }


    public function changeLeader(Request $request, $group_key, $old_leader_key)
    {
        $new_leader = $request->get('student_id');
        try {
            $old_leader_id = firebaseGetReference('users/' . $old_leader_key)->getValue();
            $new_leader_id = firebaseGetReference('users/' . $new_leader)->getValue();
            firebaseGetReference('groups/' . $group_key . '/membersStd/' . $new_leader)->remove();
            firebaseGetReference('groups/' . $group_key . '/membersStd')->update([$old_leader_key => $old_leader_id['user_id']]);
            firebaseGetReference('groups/' . $group_key)->update(['leaderStudentStd' => $new_leader_id['user_id']]);
            return redirect()
                ->route('admin.group.edit', ['group_key' => $group_key])
                ->with('success', 'تم تغيير قائد الفريق بنجاح.');
        } catch (ApiException $e) {
        }


    }
}
