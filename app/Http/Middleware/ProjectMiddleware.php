<?php

namespace App\Http\Middleware;

use Closure;
use Dingo\Api\Http\Request;

class ProjectMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Dingo\Api\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        echo (json_encode($request));
        return $next($request);
    }
}
