<?php

namespace App\Http\Controllers;


use App\Http\Requests\ExportExcelRequest;
use App\Http\Requests\RegisterStudentRequest;
use App\Imports\StudentsImport;
use Kreait\Firebase\Exception\ApiException;
use Maatwebsite\Excel\Facades\Excel;


class AdminController extends Controller
{
    public function index()
    {
        $departments = ['تطوير البرمجيات', 'علم الحاسوب', 'نظم المعلومات', 'مالتيميديا', 'موبايل', 'تكنولوجيا المعلومات'];//'','','','',''
        return view('dashboard', ['departments' => $departments]);
    }

    public function storeStudent(RegisterStudentRequest $request)
    {
        try {
            firebaseCreateData()->getReference('users')->push($request);
            return redirect()->back()->with('success', 'تم تسجيل الطالب بنجاح.');
        } catch (ApiException $e) {
            return redirect()->back()->with('error', 'حصلت مشكلة في تسجيل الطالب.');
        }
    }

    public function exportStudentExcel(ExportExcelRequest $request)
    {
        $array = Excel::toArray(new StudentsImport(), $request->file('excelFile'));
        $reference = firebaseCreateData()->getReference('users');

        foreach ($array[0] as $value) {
            if ($value[0] == 'name')
                continue;
            try {
                $reference->push([
                    'name' => $value[0],
                    'email' => $value[1],
                    'phone_number' => $value[2],
                    'std' => $value[3]
                ])->getValue();
            } catch (ApiException $e) {
                return redirect()->back()->with('error', 'حصل مشكلة في رفع الملف.');
            }
        }
        return redirect()->back()->with('success', 'تم رفع الملف بنجاح.');
    }



}
