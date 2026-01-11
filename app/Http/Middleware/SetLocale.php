<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\App;
class SetLocale
{
    
    public function handle($request, Closure $next)
    {
      
        $context = $request->attributes->get('context', 'web');

        if ($context === 'admin') {
            App::setLocale($request->session()->get('admin_locale', 'fa'));
        } else {
            App::setLocale($request->session()->get('frontend_locale', 'fa'));
        }
        return $next($request);
    }
}
