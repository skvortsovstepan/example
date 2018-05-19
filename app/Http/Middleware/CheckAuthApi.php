<?php

namespace App\Http\Middleware;

use Closure;

class CheckAuthApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(!__some_auth_service__::isLoggedIn()){
            return response('Unauthorized action.', 403);
        }
        return $next($request);
    }
}
