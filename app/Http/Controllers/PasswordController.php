<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentPasswordRequest;
use Kreait\Firebase\Exception\ApiException;
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
        try {
            $token = request()->segment(4);
            $emailed_users = firebaseGetReference('emailed_users')->getValue();
            foreach ($emailed_users as $user)
                if ($user['token'] == $token)
                    return view('createPassword');
        } catch (ApiException $e) {
        }
        return redirect()->route('home')->with('error', 'ليس لديك الصلاحية لفعل ذلك.');

    }

    public function store(StudentPasswordRequest $request)
    {

        try {

            $token = $request->get('token');
            $emailed_users = firebaseGetReference('emailed_users')->getValue();
            foreach ($emailed_users as $emailed_user)
                if ($emailed_user['token'] == $token) {
                    $user_id = $emailed_user['user_id'];
                    $emailed_user = firebaseGetReference('usersFromExcel/' . $user_id)->getValue();
                    if ($this->checkUniqueUser($emailed_user) == false) {
                        return redirect()->back()->with('error', 'بيانات هذا الحساب موجودة مسبقاً.');
                    }
                    $email = $emailed_user['email'];
                    $password = $request->get('password');
                    $uid = firebaseAuth()->createUserWithEmailAndPassword($email, $password)->uid;
                    firebaseGetReference('usersFromExcel/' . $user_id)->remove();
                    firebaseGetReference('users/' . $uid)->set($emailed_user);
                    break;
                }

            return redirect()->route('login')->with('success', 'تم حفظ كلمة السر بنجاح, يمكنك الأن الدخول للنظام.');
        } catch (AuthException $e) {
            return redirect()->route('home')->with('error', 'المستخدم موجود مسبقاً.');
        } catch (FirebaseException $e) {
        }
    }

    public function checkUniqueUser($emailed_user)
    {

        $users = firebaseGetReference('users')->getValue();

        foreach ($users as $user) {
            if ($user['user_id'] == $emailed_user['user_id']
                || $user['email'] == $emailed_user['email']
                || $user['name'] == $emailed_user['name']
                || $user['mobile_number'] == $emailed_user['mobile_number']) {
                return false;
            }
        }
        return true;
    }

}
