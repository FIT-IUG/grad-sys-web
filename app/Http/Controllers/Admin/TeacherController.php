<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\MainController;
use App\Http\Requests\UpdateTeacherRequest;
use Illuminate\Support\Arr;
use Kreait\Firebase\Exception\ApiException;
use PhpParser\Node\Expr\Array_;

class TeacherController extends MainController
{

    public function __construct()
    {
        $this->middleware('checkRole');
    }

    public function index()
    {
        $teachers = getUserByRole('teacher');
        $admins = getUserByRole('admin');
        $teachers = Arr::collapse([$teachers, $admins]);
        $groups_counter = 0;
        $teachers_info = [];
        $group_access = 0;
        try {
            $groups = firebaseGetReference('groups')->getValue();
            foreach ($teachers as $teacher_key => $teacher) {
                foreach ($groups as $key => $group) {
                    if (isset($group['teacher']) && $group['teacher'] == $teacher['user_id']) {
                        Arr::forget($groups, $key);
                        $groups_counter++;
                        $group_access++;
                    }
                    if ($group_access == sizeof($groups))
                        break;
                }
                Arr::set($groups_number, 'groups_number', $groups_counter);
                $teacher = Arr::except($teacher, ['remember_token']);
                $collapse = Arr::collapse([$groups_number, $teacher]);
                Arr::set($teachers_info, $teacher_key, $collapse);
                Arr::forget($teachers, $teacher_key);
                $groups_counter = 0;
            }
            return view('admin.teacher.index', ['teachers' => $teachers_info]);

        } catch (ApiException $e) {
        }

    }

    public function edit($user_key)
    {
        try {
            $teacher = firebaseGetReference('users/' . $user_key)->getSnapshot();
            $key = $teacher->getKey();
            $teacher = $teacher->getValue();
//            $departments = ['تطوير البرمجيات', 'علم الحاسوب', 'نظم المعلومات', 'مالتيميديا', 'موبايل', 'تكنولوجيا المعلومات'];

            if ($teacher == null)
                return redirect()->back()->with('error', 'حدثت مشكلة أثناء جلب بيانات المشرف.');

            return view('admin.teacher.edit')->with([
                'key' => $key,
                'teacher' => $teacher,
//                'departments' => $departments
            ]);
        } catch (ApiException $e) {
            return redirect()->back()->with('error', 'حدثت مشكلة أثناء جلب بيانات المشرف.');
        }
    }


    public function update(UpdateTeacherRequest $teacher)
    {

        try {
            $key = $teacher->segment(5);
            firebaseGetReference('users/' . $key)->update($teacher->validated());
            return redirect()->route('admin.teacher.index')->with('success', 'تم تحديث بيانات المشرف بنجاح.');

        } catch (ApiException $e) {
            return redirect()->route('admin.teacher.index')->with('error', 'حدثت مشكلة أثناء تعديل بيانات المشرف.');

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
            return redirect()->route('admin.teacher.index')->with('success', 'تم ترقية المشرف ' . $teacher->getChild('name')->getValue() . ' بنجاح');
        } catch (ApiException $e) {
            return redirect()->route('admin.teacher.index')->with('error', 'حدثت مشكلة أثناء ترقية المشرف.');
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
            return redirect()->route('admin.teacher.index')->with('error', 'حدثت مشكلة أثناء حذف المشرف.');
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
//        $groups_counter = 0;
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
//                Arr::set($groups_data, $groups_counter++, $group_data);
//            }
//        }
//        return $groups_data;
//    }


}
