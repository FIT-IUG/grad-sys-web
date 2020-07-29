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
            $user = firebaseGetReference('users/' . $uid);

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
            return redirect()->route('login')->with('error', 'البريد الإلكتروني أو كلمة المرور خاطئة.');
        } catch (FirebaseException $e) {
            return redirect()->route('login')->with('error', 'حدثت مشكلة في فحص البيانات، يرجى المحاولة مرة أخرى.');
        } catch (NotFoundException $exception) {
            return redirect()->route('login')->with('error', 'البريد الإلكتروني غير موجود.');
        } catch (RouteNotFoundException $exception) {
            return redirect()->route('login')->with('error', 'لم يتم العثور على حسابك.');
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
            return redirect()->route('login')->with('error', 'أنت غير مسجل.');
        } catch (ServiceException $exception) {
            return redirect()->route('login')->with('error', 'حدثت مشكلة أثناء الاتصال.');
        } catch (ApiException $e) {
            return redirect()->route('login')->with('error', 'حدثت مشكلة أثناء الاتصال.');
        }

    }
}
