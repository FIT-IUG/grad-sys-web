<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Kreait\Firebase\Exception\ApiException;

class CheckUserInCreatePassword
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

        $token = request()->segment(4);
        if ($token == null)
            $token = $request->get('token');
        $emailed_users = firebaseGetReference('emailed_users')->getValue();
        foreach ($emailed_users as $user)
            if ($user['token'] == $token)
                return $next($request);

        return redirect()->route('home')->with('error', 'ليس لديك صلاحية لفعل ذلك.');
    }
}
