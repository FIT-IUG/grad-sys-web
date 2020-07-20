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
//        $this->middleware('checkUserInCreatePassword');
    }

    public function create()
    {
        $token = request()->segment(4);
        $emailed_users = firebaseGetReference('emailed_users')->getValue();
        foreach ($emailed_users as $user)
            if ($user['token'] == $token)
                return view('student.createPassword');

        return redirect()->route('home')->with('error', 'ليس لديك الصلاحية لفعل ذلك.');

    }

    public function store(StudentPasswordRequest $request)
    {

        try {

            $token = $request->get('token');
            $emailed_users = firebaseGetReference('emailed_users')->getValue();
            foreach ($emailed_users as $user)
                if ($user['token'] == $token) {
                    $user_id = $user['user_id'];
                    $student = firebaseGetReference('users/' . $user_id)->getValue();
                    $email = $student['email'];
                    $password = $request->get('password');
                    $uid = firebaseAuth()->createUserWithEmailAndPassword($email, $password)->uid;
                    firebaseGetReference('users/' . $user_id)->remove();
                    firebaseGetReference('users/' . $uid)->set($student);
                    break;
                }

            return redirect()->route('login')->with('success', 'تم حفظ كلمة السر بنجاح, يمكنك الأن الدخول للنظام.');
        } catch (AuthException $e) {
            return redirect()->route('home')->with('error','المستخدم موجود مسبقاً.');
        } catch (FirebaseException $e) {
        }
    }
}
