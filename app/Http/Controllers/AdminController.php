<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExportExcelRequestStudents;
use App\Http\Requests\ExportExcelRequestTeachers;
use App\Http\Requests\RegisterStudentRequest;
use App\Http\Requests\SettingsRequest;
use App\Imports\StudentsImport;
use App\Imports\TeachersImport;
use Illuminate\Support\Arr;
use Kreait\Firebase\Exception\ApiException;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    public function settings()
    {
        $notifications = [];
        $statistics = '';
        $settings = firestoreCollection('settings')->documents()->rows()[0]->data();
        return view('admin.settings')->with([
            'notifications' => $notifications,
            'statistics' => $statistics,
            'settings' => $settings
        ]);
    }

    public function updateSettings(SettingsRequest $request)
    {
        dd('this is update settings');
    }


    public function storeStudent(RegisterStudentRequest $request)
    {
        $student = Arr::collapse([$request->validated(), ['role' => 'student']]);

//        dd(getLastIdForDocument('users'));
        try {
            firebase('users')->push($student);
            //Send Email
            //            firestoreCollection('users')->newDocument()->create($student);
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
//                firestoreCollection('users')->newDocument()
//                    ->create(
                firebase('users')->push([
                    'user_id' => $value[0],
                    'name' => $value[1],
                    'role' => $value[2],
                    'department' => $value[3],
                    'mobile_number' => $value[4],
                    'email' => $value[5],
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


//    public function getStudentsWithoutTeam()
//    {
//
////        get students std from groups table, from all students (members and leaders)
//        $groups = firestoreCollection('groups')->documents()->rows();
//        $leadersStd = Arr::pluck($groups, 'leaderStudentStd');
//        $members_std = Arr::pluck($groups, 'membersStd');
//        $members_std = Arr::flatten($members_std);
//
//        $registered_groups_std = Arr::collapse([$leadersStd, $members_std]);
//        $students = firestoreCollection('students')->documents()->rows();
//        $students = Arr::pluck($students, 'std');
//
//        return array_diff($students, $registered_groups_std);
//
//    }

//
//    public function test(){
//        $students = firestoreCollection('student')->documents()->rows();
//        $users = firestoreCollection('users')->documents()->rows();
//        foreach ($students as $student){
//
//        }
//        dd('this is test');
//    }
}
