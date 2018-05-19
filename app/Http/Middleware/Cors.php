<?php

namespace App\Http\Middleware;

use Closure;

class Cors
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
        $headers = [
            'Access-Control-Allow-Origin'      => \Config::get('someConfig'),
            //'Access-Control-Allow-Methods'     => 'POST, OPTIONS',
            'Access-Control-Allow-Methods'   => 'POST, GET, OPTIONS, PUT, DELETE',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Max-Age'           => '86400',
            'Access-Control-Allow-Headers'     => 'Content-Type, Authorization, X-Requested-With, Cache-Control',
            //'Access-Control-Allow-Headers'   => 'X-Custom-Header, X-Requested-With, Content-Type, Origin, Authorization, Accept, Client-security-token',
        ];

        if($request->isMethod('options')){
            return response( json_encode(['data' => 'preflight-response',
            ]),
                204,
                $headers
            );
        }

        if ($request->isMethod('post') && $request->has('data')){
            $input['data'] = json_decode($request->get('data'), true);

            $request->merge($input);
        }


        // For all other cases
        $response = $next($request);
        foreach($headers as $key => $value) {
            $response->headers->set($key, $value);
        }
        return $response;
    }
}
