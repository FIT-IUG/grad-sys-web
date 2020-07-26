<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\MainController;
use App\Http\Requests\UpdateTeacherRequest;
use Kreait\Firebase\Exception\ApiException;

class TeacherController extends MainController
{

    public function __construct()
    {
        $this->middleware('checkRole');
    }

    public function index()
    {
        $teachers = getUserByRole('teacher');

        return view('admin.teacher.index', ['teachers' => $teachers]);
    }

    public function edit($user_key)
    {
        try {
            $teacher = firebaseGetReference('users/' . $user_key)->getSnapshot();
            $key = $teacher->getKey();
            $teacher = $teacher->getValue();
//            $departments = ['تطوير البرمجيات', 'علم الحاسوب', 'نظم المعلومات', 'مالتيميديا', 'موبايل', 'تكنولوجيا المعلومات'];

            if ($teacher == null)
                return redirect()->back()->with('error', 'حصلت مشكلة في جلب بيانات المشرف.');

            return view('admin.teacher.edit')->with([
                'key' => $key,
                'teacher' => $teacher,
//                'departments' => $departments
            ]);
        } catch (ApiException $e) {
            return redirect()->back()->with('error', 'حصلت مشكلة في جلب بيانات المشرف.');
        }
    }


    public function update(UpdateTeacherRequest $teacher)
    {

        try {
            $key = $teacher->segment(5);
            firebaseGetReference('users/' . $key)->update($teacher->validated());
            return redirect()->route('admin.teacher.index')->with('success', 'تم تحديث بيانات المشرف بنجاح.');

        } catch (ApiException $e) {
            return redirect()->route('admin.teacher.index')->with('error', 'حصلت مشكلة في تعديل بيانات المشرف.');

        }

    }

    public function show($teacher_key)
    {

        try {
            $teacher = firebaseGetReference('users/' . $teacher_key)->getValue();
            $teacher_groups = $this->groupsDataForTeacher($teacher['user_id']);

            return view('admin.teacher.show')->with([
                'teacher' => $teacher,
                'teacher_groups' => $teacher_groups
            ]);

        } catch (ApiException $e) {
        }
//


    }

    public function promotion($key)
    {
        try {
            $teacher = firebaseGetReference('users/' . $key);
            $teacher->update(['role' => 'admin']);
            return redirect()->route('admin.teacher.index')->with('success', 'تم ترقية المدرس ' . $teacher->getChild('name')->getValue() . ' بنجاح');
        } catch (ApiException $e) {
            return redirect()->route('admin.teacher.index')->with('error', 'حصلت مشكلة في ترقية المشرف.');
        }
    }


    public function destroy($teacher_key)
    {
        try {
            $teacher = firebaseGetReference('users/' . $teacher_key);
            if ($teacher->getValue() != null) {
                $teacher->remove();
                return redirect()->route('admin.teacher.index')->with('success', 'تم حذف المشرف بنجاح.');
            }
            return redirect()->route('admin.teacher.index')->with('error', 'لم يتم حذف المشرف.');
        } catch (ApiException $e) {
            return redirect()->route('admin.teacher.index')->with('error', 'حصلت مشكلة في حذف المشرف.');
        }
    }

//    private function groupsData($id)
//    {
//        $teacher_id = $id;
//        $groups = firebaseGetReference('groups')->getValue();
//        $students = getUserByRole('student');
//        $groups_data = [];
//        $students_data = [];
//        $index = 0;
//        $group_counter = 0;
//
//        foreach ($groups as $group) {
//            if (isset($group['teacher']) && $group['teacher'] == $teacher_id) {
//                $group_students_std = Arr::flatten([$group['leaderStudentStd'], $group['membersStd']]);
//                foreach ($students as $student)
//                    foreach ($group_students_std as $std)
//                        if ($student['user_id'] == $std) {
//                            $student = Arr::except($student, ['remember_token', 'role']);
//                            if ($group['leaderStudentStd'] == $student['user_id']) {
//                                $student = Arr::collapse([$student, ['isLeader' => true]]);
//                            } else
//                                $student = Arr::collapse([$student, ['isLeader' => false]]);
//                            Arr::set($students_data, $index++, $student);
//                        }
//                $group_data = Arr::collapse([$group, ['students_data' => $students_data]]);
//                $students_data = [];
//                $index = 0;
//                Arr::set($groups_data, $group_counter++, $group_data);
//            }
//        }
//        return $groups_data;
//    }


}
