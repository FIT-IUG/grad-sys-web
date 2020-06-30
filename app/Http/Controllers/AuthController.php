<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Google\Cloud\Core\Exception\BadRequestException;
use Google\Cloud\Core\Exception\ServiceException;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Kreait\Firebase\Exception\ApiException;
use Kreait\Firebase\Exception\AuthException;
use Kreait\Firebase\Exception\FirebaseException;
use Google\Cloud\Core\Exception\NotFoundException;
use Faker\Generator as Faker;

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

//        createUsers($email, $password, 'student', '120169998', 'CS');

        try {
            //verify user if exist
            $uid = app('firebase.auth')->verifyPassword($email, $password)->uid;

            //create token
            $token = Str::random(60);

            //store remember token
            $user = firebaseGetReference('users/' . $uid);
            $user->update(['remember_token' => $token]);

            Session::put('uid', $uid);
            Session::put('token', $token);

            $role = getRole();

            return redirect()->route($role . '.index');
//            return redirect()->route('dashboard');
        } catch (AuthException $e) {
            return redirect()->back()->with('error', 'الايميل او كلمة السر خاطئة.')->with('email', $email);
        } catch (FirebaseException $e) {
            return redirect()->back()->with('error', 'حدثت مشكلة فحص البيانات, يرجى المحاولة مرة اخرى.');
        } catch (NotFoundException $exception) {
            return redirect()->route('logout')->with('error', 'الايميل غير موجود.');
        }
    }

    public function createUsers($email, $password, $role, $user_id, $department)
    {
        $uid = app('firebase.auth')->signInWithEmailAndPassword($email, $password)->uid;
        $factory = new Faker();
        firebaseGetReference('users/' . $uid)->set([
            'email' => $email,
            'name' => $factory->name,
            'role' => $role,
            'mobile_number' => $factory->phoneNumber,
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
