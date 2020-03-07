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
     * @throws ApiException
     */
    public function handle($request, Closure $next)
    {
        if (Session::has('userId')) {
           $firebase = firebaseCreateData();
            $id = Session::get('userId');
            $sessionToken = Session::get('token');
            $user = $firebase->getReference()->getChild('users/' . $id)->getValue();
            $token = $user['remember_token'];
            if ($sessionToken == decrypt($token))
                return $next($request);
        } else
            return redirect()->route('logout');
    }
}
