<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceJsonResponse
{
    public function handle(Request $request, Closure $next)
    {
        // Forzar JSON solo para endpoints API reales, NO para la página de documentación
        if ($request->is('api/*') && !$request->is('api/documentation') && !$request->is('api/documentation/*')) {
            $request->headers->set('Accept', 'application/json');
        }
        return $next($request);
    }
}
