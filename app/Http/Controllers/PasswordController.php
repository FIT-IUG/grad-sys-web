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
            $emailed_users = firebaseGetReference('emailedUsers')->getValue();
            foreach ($emailed_users as $user)
                if ($user['token'] == $token)
                    return view('createPassword');
        } catch (ApiException $e) {
        }
        return redirect()->route('home')->with('error', 'ليس لديك صلاحية لفعل ذلك.');

    }

    public function store(StudentPasswordRequest $request)
    {
        try {

            $token = $request->get('token');
            $emailed_users = firebaseGetReference('emailedUsers')->getValue();
            foreach ($emailed_users as $emailed_user)
                if ($emailed_user['token'] == $token) {
                    $user_id = $emailed_user['user_id'];
                    $emailed_user = firebaseGetReference('usersFromExcel/' . $user_id)->getValue();
//                   check if there is same data in firebase
                    if ($emailed_user != null && $this->checkUniqueUser($emailed_user) == false) {
                        return redirect()->back()->with('error', 'بيانات هذا الحساب موجودة مسبق.');
//                  check if emailed user has data that mean it is find user in usersFromExcel table
                    } elseif ($emailed_user != null) {
                        dd('yes');
                        $email = $emailed_user['email'];
                        $password = $request->get('password');
                        $uid = firebaseAuth()->createUserWithEmailAndPassword($email, $password)->uid;
                        firebaseGetReference('usersFromExcel/' . $user_id)->remove();
                        firebaseGetReference('users/' . $uid)->set($emailed_user);
                        break;
//                  if user did not find in usersFromExcel table this will check if user data in users table
//                  this happen when user register by admin
                    } elseif ($emailed_user == null) {
                        $user = firebaseGetReference('users/' . $user_id)->getValue();
                        if ($user != null) {
                            $email = $user['email'];
                            $password = $request->get('password');
                            $uid = firebaseAuth()->createUserWithEmailAndPassword($email, $password)->uid;
                            firebaseGetReference('users/' . $user_id)->remove();
                            firebaseGetReference('users/' . $uid)->set($user);
                            break;
                        } else {
                            return redirect()->back()->with('error', 'بياناتك غير موجودة على النظام.');
                        }
                    }
                }
            return redirect()->route('login')->with('success', 'تم حفظ كلمة المرور بنجاح، يمكنك الآن الدّخول للنظام.');
        } catch (AuthException $e) {
            return redirect()->route('home')->with('error', 'المستخدم موجود مسبقًا.');
        } catch (FirebaseException $e) {
        }
    }

    public function checkUniqueUser($emailed_user)
    {
        $users = firebaseGetReference('users')->getValue();
        foreach ($users as $user) {
            if (isset($user['user_id']))
                if ($user['user_id'] == $emailed_user['user_id'])
                    return false;
            if (isset($user['email']))
                if ($user['email'] == $emailed_user['email'])
                    return false;
            if (isset($user['name']))
                if ($user['name'] == $emailed_user['name'])
                    return false;
            if (isset($user['mobile_number']))
                if ($user['mobile_number'] == $emailed_user['mobile_number'])
                    return false;
        }
        return true;
    }

}
