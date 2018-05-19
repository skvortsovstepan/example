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

        $SomeItem2_id = $segments[count($segments)-2];

        $SomeItem5_id = SomeAuthService::id();

        $SomeItem2_ = SomeItem2::find($SomeItem2_id);

        $last_lock_update = date_create_from_format ("Y-m-d H:i:s", $SomeItem2_->SomeItem2__SomeItem9s_lock);
        $now_date = date_create(date("Y-m-d H:i:s"));

        if($SomeItem2_->SomeItem2__SomeItem9s_locked_by != $SomeItem5_id
            && $SomeItem2_->SomeItem2__SomeItem9s_locked_by
            && $last_lock_update
            && date_diff($last_lock_update, $now_date)->format("%r%i") < \Config::get('someConfig')
        ){
            return response(json_encode(['data' => false]), 200);
        }


        return $next($request);
    }
}
