<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExportExcelRequestStudents;
use App\Http\Requests\ExportExcelRequestTeachers;
use App\Http\Requests\RegisterStudentRequest;
use App\Imports\StudentsImport;
use App\Imports\TeachersImport;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Kreait\Firebase\Exception\ApiException;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    public function index()
    {
        $departments = ['تطوير البرمجيات', 'علم الحاسوب', 'نظم المعلومات', 'مالتيميديا', 'موبايل', 'تكنولوجيا المعلومات'];//'','','','',''
        $teachers = ['خالد', 'احمد', 'محمد'];
        $number_of_students = collectionSize('students');
        $number_of_groups = collectionSize('groups');
        $statistics = [
            'number_of_students' => $number_of_students,
            'number_of_groups' => $number_of_groups,
            'number_of_teamed_students' => 185
        ];
        $students = '';
        $notifications = [];
        if (hasRole('student')) {
            $groups = firestoreCollection('groups')->documents()->rows();
            $members_std = Arr::pluck($groups, 'membersStd');
            $students = $this->getStudentsWithoutTeam();

            $std = getStudentStd();

            $notification_collection = firestoreCollection('notifications');
//        if to == null that is mean there is no notifications
            $studentNotificationTo = $notification_collection->where('to', '=', $std)->documents()->rows();
//        dd(Arr::pluck($studentNotificationTo, 'isAccept'));
            if ($studentNotificationTo != null) {
                foreach ($studentNotificationTo as $studentNotification) {
                    $id = $studentNotification->id();
                    $notif = $notification_collection->document($id)->snapshot()->data();
                    if ($notif['isAccept'] == 0) {
                        $from = $notif['from'];
                        $type = $notif['type'];
                        $to = $notif['to'];
                        $from_name = firestoreCollection('students')
                            ->where('std', '=', $from)
                            ->documents()->rows()[0]->data()['name'];
                        if ($type == 'join_team') {
                            $message = 'يطلب منك الطالب ' . $from_name . ' الانضمام الى فريق التخرج الخاص به. اذا كنت موافق اضغط.';
                            $notifications = [['from' => $from, 'message' => $message, 'to' => $to, 'from_name' => $from_name]];
                        }
                    }
                }
            }
        } elseif (hasRole('supervisor')) {
            $to = 'خالد';
            $notifications_for_supervisor = firestoreCollection('notifications')
                ->where('to', '=', 'خالد')
                ->where('isAccept', '', 1)->documents()->rows();
            $i = 0;
            foreach ($notifications_for_supervisor as $notification) {
                $message = 'طلب لان تكون مشرف للمجموعة.';
                $from = $notification['from'];
                $from_name = firestoreCollection('students')->where('std', '=', $from)
                    ->documents()->rows()[0]->data()['name'];
                $initial_title = firestoreCollection('groups')->where('leaderStudentStd', '=', $from)->documents()->rows()[0]->get('initialProjectTitle');
                $notifications += [$i++ => ['from' => $from, 'message' => $message, 'to' => $to, 'from_name' => $from_name,
                    'initial_title' => $initial_title]];
            }
        }
        return view('dashboard', ['departments' => $departments, 'teachers' => $teachers,
            'statistics' => $statistics, 'students' => $students, 'notifications' => $notifications]);
    }

    public function storeStudent(RegisterStudentRequest $request)
    {

        try {
            firestoreCollection('students')->newDocument()->create($request->validated());
            return redirect()->back()->with('success', 'تم تسجيل الطالب بنجاح.');
        } catch (ApiException $e) {
            return redirect()->back()->with('error', 'حصلت مشكلة في تسجيل الطالب.');
        }
    }

    public function exportStudentsExcel(ExportExcelRequestStudents $request)
    {
        $array = Excel::toArray(new StudentsImport(), $request->file('excelFile'));
        foreach ($array[0] as $value) {
            if ($value[0] == 'name')
                continue;
            try {
                firestoreCollection('students')->newDocument()
                    ->create([
                        'name' => $value[0],
                        'email' => $value[1],
                        'phone_number' => $value[2],
                        'std' => $value[3],
                    ]);
            } catch (ApiException $e) {
                return redirect()->back()->with('error', 'حصل مشكلة في رفع الملف.');
            }
        }
        return redirect()->back()->with('success', 'تم رفع الملف بنجاح.');
    }

    public function exportTeachersExcel(ExportExcelRequestTeachers $request)
    {

        $array = Excel::toArray(new TeachersImport(), $request->file('excelFile'));
//        $supervisor = firebaseCreateData()->getReference('teachers');

        foreach ($array[0] as $value) {
            if ($value[0] == 'name')
                continue;
            try {
                firestoreCollection('teachers')->newDocument()
                    ->create([
                        'name' => $value[0],
                        'email' => $value[1],
                        'phone_number' => $value[2],
                    ]);
            } catch (ApiException $e) {
                return redirect()->back()->with('error', 'حصل مشكلة في رفع الملف.');
            }
        }
        return redirect()->back()->with('success', 'تم رفع الملف بنجاح.');
    }

    public function getStudentsWithoutTeam()
    {

//        get students std from groups table, from all students (members and leaders)
        $groups = firestoreCollection('groups')->documents()->rows();
        $leadersStd = Arr::pluck($groups, 'leaderStudentStd');
        $members_std = Arr::pluck($groups, 'membersStd');
        $members_std = Arr::flatten($members_std);

        $registered_groups_std = Arr::collapse([$leadersStd, $members_std]);
        $students = firestoreCollection('students')->documents()->rows();
        $students = Arr::pluck($students, 'std');

        return array_diff($students, $registered_groups_std);

    }

}
