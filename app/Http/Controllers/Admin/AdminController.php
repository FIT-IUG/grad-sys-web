<?php

namespace App\Http\Controllers\Admin;

use App\Events\NewStudentHasCreateEvent;
use App\Events\UploadUsersExcelFileEvent;
use App\Http\Controllers\MainController;
use App\Http\Requests\ExportExcelRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\SettingsRequest;
use App\Imports\StudentsImport;
use App\Mail\SendCreatePassword;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Kreait\Firebase\Exception\ApiException;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends MainController
{

    public function __construct()
    {
        $this->middleware('checkRole');
    }

    public function index()
    {

        $departments = ['تطوير البرمجيات', 'علم الحاسوب', 'نظم المعلومات', 'مالتيميديا', 'موبايل', 'تكنولوجيا المعلومات'];
        $notifications = $this->getNotifications();
        $number_of_students = sizeof(getUserByRole('student'));
        $teacher_groups = $this->groupsDataForTeacher(null);
        $teachers = getUserByRole('teacher');

        //Check if registered student is male(1) or female(2) by first number of there std
        $students = getStudentsStdWithoutGroup();

        $groups = firebaseGetReference('groups')->getValue();
        $number_of_groups = $groups != null ? sizeof($groups) : 'لا يوجد';

        $number_of_teamed_students = 0;

        if (isset($groups))
            foreach ($groups as $group) {
                $number_of_teamed_students++;
                if (isset($group['membersStd']) && $group['membersStd'] != null)
                    $number_of_teamed_students += sizeof($group['membersStd']);
            }
        else
            $number_of_teamed_students = 'لا يوجد';

        $statistics = [
            'number_of_students' => $number_of_students,
            'number_of_groups' => $number_of_groups,
            'number_of_teamed_students' => $number_of_teamed_students,
            'number_of_teachers' => sizeof($teachers)
        ];

        // get user id, every user have unique id

        return view('admin.index', [
            'departments' => $departments,
            'statistics' => $statistics,
            'students' => $students,
            'notifications' => $notifications,
            'teacher_groups' => $teacher_groups
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

    public function storeUser(RegisterUserRequest $request)
    {
        $student = $request->validated();
        try {
            // store in users table at first
            $student = firebaseGetReference('users')->push($student);

            // send create password email, and store token and user_id in emailed_users table
//            event(new NewStudentHasCreateEvent($student));

            $key = $student->getKey();
            $email = $request->get('email');

            //Send Email
            $token = Str::random(60);
            firebaseGetReference('emailed_users')->push([
                'user_id' => $key,
                'token' => $token
            ]);

            Mail::to($email)->send(new SendCreatePassword($token));

            return redirect()->back()->with('success', 'تم تسجيل المستخدم بنجاح.');
        } catch (ApiException $e) {
            return redirect()->back()->with('error', 'حصلت مشكلة في تسجيل المستخدم.');
        }
    }

    public function exportExcelFile(ExportExcelRequest $request)
    {
        $users = Excel::toArray(new StudentsImport(), $request->file('excelFile'));
//        event(new UploadUsersExcelFileEvent($users));
        foreach ($users[0] as $value) {
            if ($value[0] == 'id')
                continue;
            try {
                firebaseGetReference('usersFromExcel')->push([
                    'user_id' => $value[0],
                    'name' => $value[1],
                    'role' => $value[2],
                    'department' => $value[3],
                    'mobile_number' => $value[4],
                    'email' => $value[5],
                ]);

                $token = Str::random(60);
                Mail::to($value[5])->send(new SendCreatePassword($token));

            } catch (ApiException $e) {
                return redirect()->back()->with('error', 'حصل مشكلة في رفع الملف.');
            }
        }
        return redirect()->back()->with('success', 'تم رفع الملف بنجاح.');
    }

    public function updateSettings(SettingsRequest $settingsRequest)
    {
        try {
            firebaseGetReference('settings')->update($settingsRequest->validated());
            return redirect()->back()->with('success', 'تم تحديث إعدادات النظام بنجاح.');
        } catch (ApiException $e) {
            return redirect()->back()->with('error', 'حصلت مشكلة في تعديل بيانات النظام.');
        }
    }

    private function getUserNotifications()
    {
        $groups = firebaseGetReference('groups')->getValue();
        $user_notifications = firebaseGetReference('notifications')->getValue();
        $notifications = [];
        $user_id = getUserId();

        foreach ($user_notifications as $key => $notification) {
            if ($notification['to'] == $user_id && $notification['status'] == 'wait') {
                if ($notification['type'] == 'to_be_supervisor') {
                    foreach ($groups as $group) {
                        if ($group['leaderStudentStd'] == $notification['from']) {
                            $notification = Arr::collapse([$notification, ['initialProjectTitle' => $group['initialProjectTitle']]]);
                            Arr::set($notifications, $key, $notification);
                            break;
                        }
                    }
                }
            }
        }
        return $notifications;
    }

}
