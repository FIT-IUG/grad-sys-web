<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Kreait\Firebase\Exception\ApiException;

class VerifyUser
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Session::has('uid')) {
            $uid = Session::get('uid');
            $sessionToken = Session::get('token');
//            $user = firebaseCreateData()->getReference()->getChild('users/' . $uid)->getValue();
            $user = app('firebase.firestore')->database()->collection('users')->document($uid)->snapshot()->data();
            $token = $user['remember_token'];
            if ($sessionToken == $token)
                return $next($request);
        } else
            return redirect()->route('logout');
    }
}
