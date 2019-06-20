<?php

namespace App\Http\Middleware;

use App\Methods\TokenCenter;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;

class Authenticate
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $authKey = $request->header('auth-key',null);
        $token = TokenCenter::getInstance()->authToken($authKey);
        $user = User::where('token', $token)->first();
        if(empty($user)){
            abort(203);
        }
        $request->user = $user;
        return $next($request);
    }
}
