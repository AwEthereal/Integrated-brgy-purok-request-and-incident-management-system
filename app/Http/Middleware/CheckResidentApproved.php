<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckResidentApproved
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        // If user is not authenticated, redirect to login
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Skip middleware for non-resident users or already approved residents
        if ($user->role !== 'resident' || $user->is_approved) {
            return $next($request);
        }
        
        // If account is rejected, redirect to dashboard with error
        if ($user->rejected_at) {
            if ($request->route()->getName() !== 'dashboard') {
                return redirect()->route('dashboard')
                    ->with('error', 'Your account has been rejected. Please contact the barangay office for assistance.');
            }
            return $next($request);
        }
        
        // If account is not approved, only allow access to specific routes
        $allowedRoutes = [
            'dashboard', 
            'incident_reports.my_reports', 
            'incident_reports.show',
            'incident_reports.index',
            'incident_reports.create',
            'incident_reports.store'
        ];
        
        if (!in_array($request->route()->getName(), $allowedRoutes)) {
            return redirect()->route('dashboard')
                ->with('error', 'Your account is pending approval. Please wait for the purok leader to approve your account.');
        }
        
        return $next($request);
    }
}
