<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;

class SomeItem1_ProtectorFourth
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

        $SomeItem5_id = $segments[count($segments)-1];
        $SomeItem1_id = $segments[count($segments)-4];

        $SomeItem1_owner_id = SomeItem1::find($SomeItem1_id)->SomeItem1_owner_id;

        $is_default = in_array($SomeItem1_owner_id, Config::get('someConfig'));

        $checked = $SomeItem5_id == $SomeItem1_owner_id ? true : false;

        if($checked || $is_default){
            return $next($request);
        }

        return response('Unauthorized action.', 403);
    }
}
