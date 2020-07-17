<?php

namespace App\Http\Controllers;

use App\Mail\SendCreatePassword;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{

    public function show()
    {

        return view('mails.student.createPassword',['link'=>'link']);
    }

    public function mail()
    {

        $students = getUserByRole('student');

        foreach ($students as $key => $student) {
            if (isset($student['email']))
                Mail::to($student['email'])->send(new SendCreatePassword($key));
            dd('done');
        }

        return 'Email sent Successfully';

    }

}
