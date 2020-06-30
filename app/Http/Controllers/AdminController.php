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
        $role = getRole();
        dump($role);
    }

    public function index()
    {
        return 'this is index from admin controller';
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
}
