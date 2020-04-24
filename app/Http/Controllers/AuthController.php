<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Google\Cloud\Core\Exception\BadRequestException;
use Google\Cloud\Core\Exception\ServiceException;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Kreait\Firebase\Exception\AuthException;
use Kreait\Firebase\Exception\FirebaseException;
use Google\Cloud\Core\Exception\NotFoundException;

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

        //create user in auth
//        firebaseAuth()->createUserWithEmailAndPassword($email, $password);

        try {
            //verify user if exist
//            $uid = firebaseAuth()->verifyPassword($email, $password)->uid;
            $uid = firebaseCreateAuth()->verifyPassword($email, $password)->uid;
//            $uid = app('firebase.auth')->verifyPassword($email, $password)->uid;
//            dd($uid);

            // create user if not exists
//            firestoreCollection('users')->newDocument()
//                ->create(['email' => $email, 'role' => 'student']);

            //create token
            $token = Str::random(60);

            //store remember token
            $user = firestoreCollection('users')->document($uid);
            $user->update([['path' => 'remember_token', 'value' => $token]]);

            Session::put('uid', $uid);
            Session::put('token', $token);
            return redirect()->route('dashboard');
        } catch (AuthException $e) {
            return redirect()->back()->with('error', 'الايميل او كلمة السر خاطئة.')->with('email', $email);
        } catch (FirebaseException $e) {
            return redirect()->back()->with('error', 'حدثت مشكلة فحص البيانات, يرجى المحاولة مرة اخرى.');
        } catch (NotFoundException $exception) {
            return redirect()->route('logout')->with('error', 'الايميل غير موجود.');
        }
    }

    public function logout()
    {
        try {
            //      remove remember token value and clear sessions
            firestoreCollection('users')
                ->document(session()->get('uid'))
                ->update([['path' => 'remember_token', 'value' => '']]);
//      clear session
            Session::remove('uid');
            Session::remove('token');

            return redirect()->route('login');
        } catch (BadRequestException $exception) {
            return redirect()->route('login')->with('error', 'انت غير مسجل.');
        } catch (ServiceException $exception) {
            return redirect()->route('login')->with('error', 'حصلت مشكلة في الاتصال.');
        }

    }
}
