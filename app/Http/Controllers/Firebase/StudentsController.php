<?php

namespace App\Http\Controllers\Firebase;

use App\Exports\StudentsExport;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\UploadedFile;

class StudentsController extends MainController
{


    public function export()
    {
        dd(Excel::download(new StudentsExport(), 'students.xlsx'));
        $reference = $this->database->getReference('users')->getValue();
        return $reference;
//        return Excel::download(new StudentsExport,'students.xlsx');
    }

    public function view()
    {
        return view('export', ['users']);
    }

    public function import()
    {
        $array = Excel::toArray(new UsersImport(), request()->file('students'));
        $reference = $this->database->getReference('users');
        foreach ($array[0] as $value) {
            $reference->push([
                'name' => $value[0],
                'email' => $value[1],
                'phone_number' => $value[2],
                'std' => $value[3]
            ]);
            dd($value[0]);
        }
        dd($array[0]);
    }
}
