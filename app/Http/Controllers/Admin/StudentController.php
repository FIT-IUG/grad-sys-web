<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateStudentRequest;
use Kreait\Firebase\Exception\ApiException;

class StudentController extends Controller
{

    public function __construct()
    {
        $this->middleware('checkRole');
    }

    public function index()
    {
        $students = getUserByRole('student');

        return view('admin.student.index', ['students' => $students]);
    }

    public function edit($user_id)
    {
        try {
            $student = firebaseGetReference('users/' . $user_id)->getSnapshot();
            $key = $student->getKey();
            $student = $student->getValue();
            $departments = firebaseGetReference('departments')->getValue();

            if ($student == null)
                return redirect()->back()->with('error', 'حدثت مشكلة أثناء جلب بيانات الطالب.');

            return view('admin.student.edit')->with([
                'key' => $key,
                'student' => $student,
                'departments' => $departments
            ]);
        } catch (ApiException $e) {
            return redirect()->back()->with('error', 'حدثت مشكلة أثناء جلب بيانات الطالب.');
        }
    }

    public function update(UpdateStudentRequest $student)
    {

        try {
            $key = $student->segment(5);
            firebaseGetReference('users/' . $key)->update($student->validated());
            return redirect()->route('admin.student.index')->with('success', 'تم تحديث بيانات الطالب بنجاح.');

        } catch (ApiException $e) {
            return redirect()->route('admin.student.index')->with('error', 'حدثت مشكلة أثناء تعديل بيانات الطالب.');

        }

    }

    public function destroy($user_key)
    {
        try {
            $student = firebaseGetReference('users/' . $user_key);
            if ($student->getValue() != null) {
                $student->remove();
                return redirect()->route('admin.student.index')->with('success', 'تم حذف الطالب بنجاح.');
            }
            return redirect()->route('admin.student.index')->with('error', 'لم يتم حذف الطالب.');
        } catch (ApiException $e) {
            return redirect()->route('admin.student.index')->with('error', 'حدثت مشكلة أثناء حذف الطالب.');
        }

    }
}
