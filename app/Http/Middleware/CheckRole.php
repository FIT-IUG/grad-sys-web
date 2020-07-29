<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
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
        $changed_role = $request->segments()[1];
        $role = getRole();
//        dd($changed_role);
        if ($changed_role != $role) {
            return redirect()->route($role.'.index')->with('error', 'ليس لديك صلاحية لفعل ذلك.');
        }
        return $next($request);
    }
}
