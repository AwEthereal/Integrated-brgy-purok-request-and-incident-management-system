<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

class SetIntendedUrl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only set intended URL for GET requests that are not AJAX and not from the API
        if ($request->isMethod('GET') && !$request->ajax() && !$request->is('api/*')) {
            // Only set intended URL for specific paths
            $trackedPaths = [
                'dashboard',
                'requests*',
                'incident-reports*',
                'notifications*',
            ];

            if ($request->is($trackedPaths)) {
                Session::put('url.intended', URL::current());
            }
        }

        return $next($request);
    }
}
