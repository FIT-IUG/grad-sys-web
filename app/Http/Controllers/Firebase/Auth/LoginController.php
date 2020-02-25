<?php

namespace App\Http\Controllers\Firebase\Auth;

use App\Http\Controllers\Firebase\MainController;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Kreait\Firebase\Database\Transaction;

class LoginController extends MainController
{
    private $email;


    public function index()
    {
        return view('login');
    }

    public function check(LoginRequest $request)
    {

        $reference = $this->database->getReference('users');
        $data = Arr::except($reference->getValue(), null);
        $emails = Arr::pluck($data, 'email', 'std');
        $index = 0;
        foreach ($emails as $std => $email) {
            $index++;
            if ($request->email == $email) {
                $this->email = $email;

                $password = Arr::get($data, '' . $index . '.password');
                $login_u = [];
                if ($request->password == $password) {
                    foreach ($data as $user) {
                        if ($user['email'] == $this->email) {
                            $login_u = $user;
                        }
                    }
                    return view('home')->with('user', $login_u);
                } else {
                    dd('hh');
                    return redirect()->back()->with('error', 'wrong password');
                }
            }
        }

    }
}
