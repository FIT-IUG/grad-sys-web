<?php

namespace App\Http\Controllers;

use App\Mail\SendCreatePassword;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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
            $token = Str::random('60');
            firebaseGetReference('emailed_users')->push([
                'user_id' => $key,
                'token' => $token]);
            if (isset($student['email']))
                Mail::to($student['email'])->send(new SendCreatePassword($token));
            dd('done');
        }

        return 'Email sent Successfully';

    }

}
