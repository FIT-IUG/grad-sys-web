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
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Kreait\Firebase\Exception\ApiException;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends MainController
{

    public function __construct()
    {
        $this->middleware('checkRole');
    }

    public function index()
    {

        $departments = ['تطوير البرمجيات', 'علم الحاسوب', 'نظم المعلومات', 'وسائط متعددة', 'برمجة تطبيقات الهاتف', 'تكنولوجيا المعلومات'];
        $notifications = $this->getNotifications();
        $number_of_students = sizeof(getUserByRole('student'));
        $teacher_groups = $this->groupsDataForTeacher(null);
        $teachers = getUserByRole('teacher');

        //Check if registered student is male(1) or female(2) by first number of there std
        $students = getStudentsStdWithoutGroup();

        $groups = firebaseGetReference('groups')->getValue();
        $number_of_groups = $groups != null ? sizeof($groups) : '0';

        $number_of_teamed_students = 0;

        if ($groups != null)
            foreach ($groups as $group) {
                $number_of_teamed_students++;
                if (isset($group['membersStd']) && $group['membersStd'] != null)
                    $number_of_teamed_students += sizeof($group['membersStd']);
            }
        else
            $number_of_teamed_students = '0';

        $statistics_departments = $this->getDepartmentsStatistic();
        $statistics = [
            'number_of_students' => $number_of_students,
            'number_of_groups' => $number_of_groups,
            'number_of_teamed_students' => $number_of_teamed_students,
            'number_of_teachers' => sizeof($teachers),
            'departments' => $statistics_departments['departments'],
            'departments_data' => $statistics_departments['departments_data'],
            'students' => $this->getNumberOfStudentsInDepartments()
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

        $notifications = $this->getNotifications();
        try {
            $settings = firebaseGetReference('settings')->getValue();
            $tags = firebaseGetReference('tags')->getValue();
            $t_tags = [];
            foreach ($tags as $tag) {
                Arr::set($t_tags, $tag, 0);
            }
            $groups = firebaseGetReference('groups')->getValue();
            foreach ($groups as $group) {
                if (isset($group['tags']) && $group['tags'] != null)
                    foreach ($group['tags'] as $tag) {
                        $t_tags[$tag]++;
                    }
            }
            $com_tags = [];
            foreach ($tags as $tag_key => $tag) {
                foreach ($t_tags as $key => $frequency_use) {
                    if ($tag == $key) {
                        Arr::set($com_tags, $tag_key, [
                            'name' => $tag,
                            'frequency_use' => $frequency_use
                        ]);
                    }
                }
            }

            return view('admin.settings')->with([
                'notifications' => $notifications,
                'settings' => $settings,
                'tags' => $com_tags
            ]);
        } catch (ApiException $e) {
        }


    }

    public function storeUser(RegisterUserRequest $request)
    {
        $student = $request->validated();
        try {
            // store in users table at first
            $student = firebaseGetReference('users')->push($student);

            // send create password email, and store token and user_id in emailedUsers table
//            event(new NewStudentHasCreateEvent($student));

            $key = $student->getKey();
            $email = $request->get('email');

            //Send Email
            $token = Str::random(60);
            firebaseGetReference('emailedUsers')->push([
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
                $key = firebaseGetReference('usersFromExcel')->push([
                    'user_id' => $value[0],
                    'name' => $value[1],
                    'role' => $value[2],
                    'department' => $value[3],
                    'mobile_number' => $value[4],
                    'email' => $value[5],
                ])->getKey();

                $token = Str::random(60);
                firebaseGetReference('emailedUsers')->push([
                    'user_id' => $key,
                    'token' => $token
                ]);
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
        if ($groups != null)
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

    public function replayToBeSupervisor(Request $request)
    {
        $this->replayToBeSupervisorRequest($request);
    }

    private function getDepartmentsStatistic()
    {

        $departments = [[1, 'تطوير البرمجيات'], [2, 'علم الحاسوب'], [3, 'نظم المعلومات'], [4, 'وسائط متعددة'], [5, 'برمجة تطبيقات الهاتف'], [6, 'تكنولوجيا المعلومات']];

        $departments_name = "[";
        foreach ($departments as $department) {
            $departments_name .= "[";
            $departments_name .= $department[0] . ",";
            $departments_name .= "'" . $department[1] . "'";
            $departments_name .= "], ";
        }
        $departments_name .= "]";

        $groups_members_departments = $this->getGroupMembersDepartments();
        $departments_data = $this->arrayToStringConverter($groups_members_departments);

        return ['departments' => $departments_name, 'departments_data' => $departments_data];

    }

}
