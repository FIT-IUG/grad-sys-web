<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Session;
use Kreait\Firebase\Exception\AuthException;
use Kreait\Firebase\Exception\FirebaseException;

class AuthController extends Controller
{
    public function login()
    {
        return view('login');
    }

    public function check(LoginRequest $request)
    {

        $auth = firebaseCreateAuth();
        $email = $request->get('email');
        $password = $request->get('password');

        try {
//            $auth->createUserWithEmailAndPassword($email, $password);
            //verify user if exist
            $uid = $auth->verifyPassword($email, $password)->uid;
            $token = $auth->createCustomToken($uid)->getPayload();
            //get user
            $users = firebaseCreateData()->getReference()->getChild('users')->getValue();
            $userToAddToken = firebaseCreateData();
            //search at user by email and add remember token to every login user
            foreach ($users as $key => $user) {
                if ($user['email'] == $email) {
                    $userWithToken = Arr::set($user, 'remember_token', encrypt($token));
                    $userToAddToken->getReference('users/' . $key)->set($userWithToken);
                    Session::put('userId', $key);
                    Session::put('token', $token);
                    return redirect()->route('dashboard');
                }
            }
            return redirect()->back()->with('error', 'الايميل او كلمة السر خاطئة.');
        } catch (AuthException $e) {
            return redirect()->back()->with('error', 'الايميل او كلمة السر خاطئة.');
        } catch (FirebaseException $e) {
            return redirect()->back()->with('error', 'حدثت مشكلة فحص البيانات, يرجى المحاولة مرة اخرى.');
        }
    }

    public function logout()
    {
        //remove remember token value and clear sessions
        firebaseCreateData()->getReference('users/' . session()->get('userId'))
            ->update(['remember_token' => '']);
        Session::remove('userId');
        Session::remove('token');
        return redirect()->route('login');

    }
}
