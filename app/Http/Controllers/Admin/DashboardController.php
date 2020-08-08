<?php

namespace App\Http\Controllers\Admin;

use App\Events\NewStudentHasCreateEvent;
use App\Events\UploadUsersExcelFileEvent;
use App\Http\Controllers\MainController;
use App\Http\Requests\ExportExcelRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\SettingsRequest;
use App\Imports\StudentsImport;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
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

        $tags = firebaseGetReference('tags')->getValue();
        $tags = $this->arrayToStringConverter($tags);
        $departments = firebaseGetReference('departments')->getValue();
        $notifications = $this->getNotifications();
        $number_of_students = sizeof(getUserByRole('student'));
        $teacher_groups = $this->groupsDataForTeacher(null);
        $teachers = $this->getTeachersCanBeSupervisor();
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
        $tags_data = $this->getTagsUse();
        $tags_data = $this->arrayToStringConverter($tags_data);

        $statistics_departments = $this->getDepartmentsStatistic();

        $statistics = [
            'number_of_students' => $number_of_students,
            'number_of_groups' => $number_of_groups,
            'number_of_teamed_students' => $number_of_teamed_students,
            'number_of_teachers' => sizeof($teachers),
            'departments' => $statistics_departments['departments'],
            'departments_data' => $statistics_departments['departments_data'],
            'tags' => $tags,
            'tags_data' => $tags_data
        ];
        // get user id, every user have unique id

        return view('admin.index', [
            'departments' => $departments,
            'statistics' => $statistics,
            'students' => $students,
            'notifications' => $notifications,
            'teacher_groups' => $teacher_groups,
        ]);
    }

    public function settings()
    {

        $notifications = $this->getNotifications();
        try {
            $settings = firebaseGetReference('settings')->getValue();
            $tags = firebaseGetReference('tags')->getValue();
            $departments = firebaseGetReference('departments')->getValue();
            $t_tags = $this->getTagsUse();
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
                'tags' => $com_tags,
                'departments' => $departments
            ]);
        } catch (ApiException $e) {
        }


    }

    public function storeUser(RegisterUserRequest $request)
    {
        $user = $request->validated();
        try {
            // store in users table at first
            $key = firebaseGetReference('users')->push($user)->getKey();

            // send create password email, and store token and user_id in emailedUsers table
            event(new NewStudentHasCreateEvent($user['email'], $key));

            return redirect()->route('admin.index')->with('success', 'تم تسجيل المستخدم بنجاح.');
        } catch (ApiException $e) {
            return redirect()->back()->with('error', 'حدثت مشكلة أثناء تسجيل المستخدم.');
        }
    }

    public function exportExcelFile(ExportExcelRequest $request)
    {
        $users = Excel::toArray(new StudentsImport(), $request->file('excelFile'));

        event(new UploadUsersExcelFileEvent($users));

        return redirect()->back()->with('success', 'تم رفع الملف بنجاح.');
    }

    public function updateSettings(SettingsRequest $settingsRequest)
    {
        try {
            firebaseGetReference('settings')->update($settingsRequest->validated());
            return redirect()->back()->with('success', 'تم تحديث إعدادات النظام بنجاح.');
        } catch (ApiException $e) {
            return redirect()->back()->with('error', 'حدثت مشكلة أثناء تعديل إعدادات النظام.');
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
        dd('hh');
        $this->replayToBeSupervisorRequest($request);
        $reply = $request->get('reply');

        if ($reply == 'success')
            return redirect()->route(getRole() . '.index')->with('success', 'تم قبول الطلب بنجاح.');
        elseif ($reply = 'reject')
            return redirect()->route(getRole() . '.index')->with('success', 'تم رفض الطلب.');
    }

    private function getDepartmentsStatistic()
    {

        try {
            $firebase_departments = firebaseGetReference('departments')->getValue();
            $departments = [];
            $index = 1;
            foreach ($firebase_departments as $department_key => $department) {
                if (strtoupper($department) == '') {
                    Arr::forget($departments, $department_key);
                } else
                    Arr::set($departments, $index++, $department);
            }

            $departments_name = "[";
            foreach ($departments as $key => $department) {
                $departments_name .= "[";
                $departments_name .= $key . ", ";
                $departments_name .= "'" . $department . "'";
                $departments_name .= "], ";
            }
            $departments_name .= "]";

            $departments_data = $this->arrayToStringConverter($this->getNumberOfStudentsInDepartments());
            return ['departments' => $departments_name, 'departments_data' => $departments_data];
        } catch (ApiException $e) {
        }

    }

    private function getTagsUse()
    {

        $tags = firebaseGetReference('tags')->getValue();
        $tags_data = [];
        foreach ($tags as $tag) {
            Arr::set($tags_data, $tag, 0);
        }
        $groups = firebaseGetReference('groups')->getValue();
        foreach ($groups as $group) {
            if (isset($group['tags']) && $group['tags'] != null)
                foreach ($group['tags'] as $tag) {
                    $tags_data[$tag]++;
                }
        }
        return $tags_data;
    }
}
