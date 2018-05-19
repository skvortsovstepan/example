<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;

class __some_item_1___ProtectorFourth
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

        $__some__item__5__id = $segments[count($segments)-1];
        $__some_item_1___id = $segments[count($segments)-4];

        $__some_item_1___owner_id = __some_item_1__::find($__some_item_1___id)->__some_item_1___owner_id;

        $is_default = in_array($__some_item_1___owner_id, Config::get('__some_config__'));

        $checked = $__some__item__5__id == $__some_item_1___owner_id ? true : false;

        if($checked || $is_default){
            return $next($request);
        }

        return response('Unauthorized action.', 403);
    }
}
