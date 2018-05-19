<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Config;

class SomeItem1Img
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

        $SomeItem1_id = $segments[count($segments)-1];

        $SomeItem5_id = SomeAuthService::id();

        $is_default = in_array($SomeItem5_id, Config::get('someConfig'));

        $owner_id = SomeItem1::find($SomeItem1_id)->SomeItem1_owner_id;

        $is_defaults = in_array($owner_id, Config::get('someConfig'));

        $checked = false;

        if($SomeItem5_id == $owner_id || $is_default || $is_defaults){
            $checked = true;
        }

        if($checked){
            return $next($request);
        }

        return response('Unauthorized action.', 403);

    }
}
