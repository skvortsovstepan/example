<?php

namespace App\Http\Middleware;

use Closure;

class CheckLock
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

        $__some_item_2___id = $segments[count($segments)-2];

        $__some__item__5__id = __some_auth_service__::id();

        $__some_item_2___ = __some_item_2__::find($__some_item_2___id);

        $last_lock_update = date_create_from_format ("Y-m-d H:i:s", $__some_item_2___->__some_item_2______some_item_9__s_lock);
        $now_date = date_create(date("Y-m-d H:i:s"));

        if($__some_item_2___->__some_item_2______some_item_9__s_locked_by != $__some__item__5__id
            && $__some_item_2___->__some_item_2______some_item_9__s_locked_by
            && $last_lock_update
            && date_diff($last_lock_update, $now_date)->format("%r%i") < \Config::get('__some_config__')
        ){
            return response(json_encode(['data' => false]), 200);
        }


        return $next($request);
    }
}
