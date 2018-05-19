<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;

class __some_item_4__PermissionsSecond
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
        $__some__item__5__id = __some_auth_service__::id();

        $segments = $request->segments();

        $__some_item_4__ = __some_item_2__::find($segments[count($segments)-2]);

        if(!$__some_item_4__){
            return response('Not found.', 404);
        }

        if(substr($__some_item_4__->__some_item_2___path, 0, stripos($__some_item_4__->__some_item_2___path, '/')) == $__some__item__5__id){
            return $next($request);
        }

        return response('Unauthorized action.', 403);
    }
}
