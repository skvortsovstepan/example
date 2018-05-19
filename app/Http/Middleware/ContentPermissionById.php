<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;

class ContentPermissionById
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
        $segments = $request->segments();

        $id = $segments[count($segments)-1];

        $__some__item__5__id = __some__auth_service__::id();

        $checked = $__some__item__5__id==$id?true:false;

        if($checked){
            return $next($request);
        }

        return response('Unauthorized action.', 403);
    }
}
