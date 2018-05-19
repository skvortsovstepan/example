<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Config;
use Closure;

class BuildOrUpdate__some_item_1__
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

        $is_default = in_array($__some__item__5__id, Config::get('__some_config__'));

        if(isset($request['0']['__some_item_1___id']) &&
            __some_item_1__::find($request['0']['__some_item_1___id'])->__some_item_1___owner_id != $__some__item__5__id &&
            !$is_default
        )
        {
            return response('Unauthorized action.', 403);
        }

        return $next($request);
    }
}
