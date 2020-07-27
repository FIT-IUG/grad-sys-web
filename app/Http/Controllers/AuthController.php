<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Carbon\Carbon;
use Google\Cloud\Core\Exception\BadRequestException;
use Google\Cloud\Core\Exception\ServiceException;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Kreait\Firebase\Exception\ApiException;
use Kreait\Firebase\Exception\AuthException;
use Kreait\Firebase\Exception\FirebaseException;
use Google\Cloud\Core\Exception\NotFoundException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class AuthController extends Controller
{
    public function login()
    {
        return view('login');
    }

    public function check(LoginRequest $request)
    {

        $email = $request->get('email');
        $password = $request->get('password');

//        createUsers();

        try {
            firebaseAuth()->signInWithEmailAndPassword($email, $password);
            $uid = firebaseAuth()->getUserByEmail($email)->uid;
//            $time_stamp = firebaseAuth()->getUser($uid)->metadata->lastLoginAt->getTimestamp();
//            $now = Carbon::now()->timestamp;
            $user = firebaseGetReference('users/' . $uid);
//            $can_login = false;

//            if ($time_stamp + 900 < $now)
//                $can_login = true;

//            if there is remember token and last login for this user less than 15m then if will fire
//            if ($user->getChild('remember_token')->getValue() != null && !$can_login) {
//                return redirect()->route('login')->with('error', 'يوجد شخص يستخدم هذا الحساب الآن.');
//            }

            //verify user if exist
//            firebaseAuth()->signInWithEmailAndPassword($email, $password)->uid;

            //create token
            $token = Str::random(60);

//            Check if this account has login

            //store remember token
            $user->update(['remember_token' => $token]);


            Session::put('uid', $uid);
            Session::put('token', $token);

            $role = getRole();

            return redirect()->route($role . '.index');
        } catch (AuthException $e) {
            return redirect()->route('login')->with('error', 'الايميل او كلمة السر خاطئة.');
        } catch (FirebaseException $e) {
            return redirect()->route('login')->with('error', 'حدثت مشكلة فحص البيانات, يرجى المحاولة مرة اخرى.');
        } catch (NotFoundException $exception) {
            return redirect()->route('login')->with('error', 'الايميل غير موجود.');
        } catch (RouteNotFoundException $exception) {
            return redirect()->route('login')->with('error', 'لم يتم إيجاد حسابك.');
        }
    }

    public function createUsers($email, $password, $role, $user_id, $department, $mobile_number)
    {
        $uid = app('firebase.auth')->signInWithEmailAndPassword($email, $password)->uid;

        firebaseGetReference('users/' . $uid)->set([
            'email' => $email,
            'name' => 'student' . $user_id,
            'role' => $role,
            'mobile_number' => $mobile_number,
            'user_id' => $user_id,
            'department' => $department
        ]);
        return 'user created successfully';
    }

    public function logout()
    {
        try {
            //      remove remember token value and clear sessions
            $uid = session()->get('uid');
            if ($uid != null)
                firebaseGetReference('users/' . $uid)->update(['remember_token' => '']);

            //      clear session
            Session::remove('uid');
            Session::remove('token');

            return redirect()->route('login');
        } catch (BadRequestException $exception) {
            return redirect()->route('login')->with('error', 'انت غير مسجل.');
        } catch (ServiceException $exception) {
            return redirect()->route('login')->with('error', 'حصلت مشكلة في الاتصال.');
        } catch (ApiException $e) {
            return redirect()->route('login')->with('error', 'حصلت مشكلة في الاتصال.');
        }

    }
}
