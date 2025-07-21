<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class AddAuditLoggingData
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // If no controller responds to request then return the response
        if (Route::currentRouteAction() == null) {
            return $response;
        }

        // Otherwise if there's a specific controller responding then
        // ensure that route has a "name" attribute specified.
        $routeName = Route::currentRouteName();

        if ($routeName == null) {
            throw new \Exception("Route name unspecified", 1);
        }

        $response->headers->set('X-Log-Action', $routeName);

        return $response;
    }
}
