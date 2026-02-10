<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class SuperAdminMiddleware
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role === 'barangay_captain') {
            return $next($request);
        }

        \Log::info('SuperAdminMiddleware: access denied for user ID ' . Auth::id());
        abort(403);
    }
}
