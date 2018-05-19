<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Config;
use Closure;

class BuildOrUpdate__SomeItem_1__
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

        $is_default = in_array($SomeItem5_id, Config::get('someConfig'));

        if(isset($request['0']['SomeItem1_id']) &&
            SomeItem1::find($request['0']['SomeItem1_id'])->SomeItem1_owner_id != $SomeItem5_id &&
            !$is_default
        )
        {
            return response('Unauthorized action.', 403);
        }

        return $next($request);
    }
}
