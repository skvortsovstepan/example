<?php

namespace App\Http\Middleware;

use App\SomeItem2;
use Closure;
use Illuminate\Support\Facades\Config;

class SomeItem4Permissions
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

        $SomeItem5_id = SomeAuthService::id();

        $segments = $request->segments();

        $SomeItem4 = SomeItem2::find($segments[count($segments)-1]);

        if(!$SomeItem4){
            return response('Not found.', 404);
        }

        if(substr($SomeItem4->SomeItem2_path, 0, stripos($SomeItem4->SomeItem2_path, '/')) == $SomeItem5_id){
            return $next($request);
        }

        return response('Unauthorized action.', 403);
    }
}
