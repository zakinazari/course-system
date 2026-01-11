<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventDirectAuthRoutes
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        
         // فقط GET login و GET register مسدود شوند
        if ($request->isMethod('get') && ($request->is('login') || $request->is('register'))) {

            if (auth()->check()) {
                return redirect()->route('dashboard');
            }

            return redirect('/');
        }

        return $next($request);
    }
}
