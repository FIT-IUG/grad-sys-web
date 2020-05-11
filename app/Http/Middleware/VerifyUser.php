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
        if (Session::has('uid')) {
            $uid = Session::get('uid');
            $sessionToken = Session::get('token');
            $token = firebaseGetReference('users/'.$uid)->getChild('remember_token')->getValue();
            if ($sessionToken == $token)
                return $next($request);
        } else
            return redirect()->route('logout');
    }
}
