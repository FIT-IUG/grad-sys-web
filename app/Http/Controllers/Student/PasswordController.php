<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentPasswordRequest;
use Kreait\Firebase\Exception\AuthException;
use Kreait\Firebase\Exception\FirebaseException;

class PasswordController extends Controller
{

    public function __construct()
    {
        $this->middleware('checkUserInCreatePassword');
    }

    public function create()
    {

        return view('student.createPassword');
    }

    public function store(StudentPasswordRequest $request)
    {

        try {
            $key = $request->get('token');
            $student = firebaseGetReference('users/' . $key)->getValue();
            $email = $student['email'];
            $password = $request->get('password');
            $uid = firebaseAuth()->createUserWithEmailAndPassword($email, $password)->uid;
            firebaseGetReference('users/' . $key)->remove();
            firebaseGetReference('users/' . $uid)->set($student);

            return redirect()->route('home')->with('success', 'تم حفظ كلمة السر بنجاح, يمكنك الأن الدخول للنظام.');
        } catch (AuthException $e) {
        } catch (FirebaseException $e) {
        }
    }
}
