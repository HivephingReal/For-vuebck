<?php

namespace App\Http\Middleware;

use Closure;

class Cc
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
        // return $next($request);
        header("Access-Control-Allow-Origin: *");
        // ALLOW OPTIONS METHOD


        $response = $next($request);

        return $response;
    }
}
