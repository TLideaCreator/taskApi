<?php

namespace App\Http\Middleware;

use App\Methods\ProjectMethod;
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
        $projectId =$request->route()[2]['projectId'];
        $check = ProjectMethod::authUserForProject($request->user->id, $projectId);
        if(!$check){
            abort(403);
        }
        return $next($request);
    }
}
