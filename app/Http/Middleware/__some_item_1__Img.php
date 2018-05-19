<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Config;

class __some_item_1__Img
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

        $__some_item_1___id = $segments[count($segments)-1];

        $__some__item__5__id = __some_auth_service__::id();

        $is_default = in_array($__some__item__5__id, Config::get('__some_config__'));

        $owner_id = __some_item_1__::find($__some_item_1___id)->__some_item_1___owner_id;

        $is_defaults = in_array($owner_id, Config::get('__some_config__'));

        $checked = false;

        if($__some__item__5__id == $owner_id || $is_default || $is_defaults){
            $checked = true;
        }

        if($checked){
            return $next($request);
        }

        return response('Unauthorized action.', 403);

    }
}
