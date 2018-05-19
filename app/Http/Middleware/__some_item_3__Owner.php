<?php

namespace App\Http\Middleware;

use Closure;

class __some_item__3_Owner
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
        $group_id = $request->get('*');

        $group = __some_item_3__::find($group_id);

        if(!$group){
            return response(' *** not found', 404);
        }

        if($group->__some_item__3___owner_id != __some_auth_service__::id()){
            return response('Unauthorized action', 403);
        }


        return $next($request);
    }
}
