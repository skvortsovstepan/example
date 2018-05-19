<?php

namespace App\Http\Middleware;

use Closure;

class SomeItem3Owner
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

        $group = SomeItem3::find($group_id);

        if(!$group){
            return response(' *** not found', 404);
        }

        if($group->SomeItem3__owner_id != SomeAuthService::id()){
            return response('Unauthorized action', 403);
        }


        return $next($request);
    }
}
