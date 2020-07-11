<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExportExcelRequestStudents;
use App\Http\Requests\RegisterStudentRequest;
use App\Http\Requests\SettingsRequest;
use App\Imports\StudentsImport;
use Illuminate\Support\Arr;
use Kreait\Firebase\Exception\ApiException;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{

    public function __construct()
    {
        $this->middleware('checkRole');
    }

    public function index()
    {

        $departments = ['تطوير البرمجيات', 'علم الحاسوب', 'نظم المعلومات', 'مالتيميديا', 'موبايل', 'تكنولوجيا المعلومات'];

        $number_of_students = sizeof(getUserByRole('student'));

        //Check if registered student is male(1) or female(2) by first number of there std
        $students = getStudentsStdWithoutGroup();

        $groups = firebaseGetReference('groups')->getValue();
        $number_of_groups = $groups != null ? sizeof($groups) : 0;

        $number_of_teamed_students = 20;
        $statistics = [
            'number_of_students' => $number_of_students,
            'number_of_groups' => $number_of_groups,
            'number_of_teamed_students' => $number_of_teamed_students
        ];

        // get user id, every user have unique id

        return view('admin.index', [
            'departments' => $departments,
            'statistics' => $statistics,
            'students' => $students,
            'notifications' => $this->getUserNotifications(),
        ]);
    }

    public function settings()
    {
        $notifications = [];
        $statistics = '';
        $settings = firebaseGetReference('settings')->getValue();
        return view('admin.settings')->with([
            'notifications' => $notifications,
            'statistics' => $statistics,
            'settings' => $settings
        ]);
    }

    public function storeStudent(RegisterStudentRequest $request)
    {
        $student = Arr::collapse([$request->validated(), ['role' => 'student']]);
        try {
            firebaseGetReference('users')->push($student);
            //Send Email
            return redirect()->back()->with('success', 'تم تسجيل الطالب بنجاح.');
        } catch (ApiException $e) {
            return redirect()->back()->with('error', 'حصلت مشكلة في تسجيل الطالب.');
        }
    }

    public function exportStudentsExcel(ExportExcelRequestStudents $request)
    {
        $array = Excel::toArray(new StudentsImport(), $request->file('excelFile'));
        foreach ($array[0] as $value) {
            if ($value[0] == 'id')
                continue;
            try {
                firebaseGetReference('users')->push([
                    'user_id' => $value[0],
                    'name' => $value[1],
                    'role' => $value[2],
                    'department' => $value[3],
                    'mobile_number' => $value[4],
                    'email' => $value[5],
                ]);
//                send email
            } catch (ApiException $e) {
                return redirect()->back()->with('error', 'حصل مشكلة في رفع الملف.');
            }
        }
        return redirect()->back()->with('success', 'تم رفع الملف بنجاح.');
    }

    public function updateSettings(SettingsRequest $settingsRequest)
    {
        firebaseGetReference('settings')->set($settingsRequest->validated());
        return redirect()->back()->with('success', 'تم تحديث إعدادات النظام بنجاح.');
    }

    private function getUserNotifications()
    {

        $user_notifications = firebaseGetReference('notifications')->getValue();
        $notifications = [];
        $index = 0;
        $user_id = getUserId();
        foreach ($user_notifications as $notification) {
            if ($notification['to'] == $user_id && $notification['status'] == 'wait') {
                if ($notification['type'] == 'to_be_supervisor') {
                    $students_data = getUserByRole('student');
                    $from_id = $notification['from'];
                    $from_name = $notification['from_name'];
                    $project_initial_title = $notification['project_initial_title'];
//                    foreach ($students_data as $student) {
//                        if ($student['user_id'] == $from_id) {
//                            $from_name = $student['name'];
//                            break;
//                        }
//                    }
                    $teacher_notification = Arr::collapse([
                        $notification,
                        ['from_name' => $from_name, 'initial_title' => $project_initial_title]
                    ]);
                    Arr::set($notifications, $index++, $teacher_notification);
                } else
                    Arr::set($notifications, $index++, $notification);
            }
        }
        return $notifications;
    }

}
