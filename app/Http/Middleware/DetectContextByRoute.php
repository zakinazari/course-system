<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DetectContextByRoute
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
       
        if ($request->route() && $request->route()->middleware() && in_array('auth', $request->route()->middleware())) {
            $request->attributes->set('context', 'admin');
        } else {
            $request->attributes->set('context', 'web');
        }

        return $next($request);
    }
}
