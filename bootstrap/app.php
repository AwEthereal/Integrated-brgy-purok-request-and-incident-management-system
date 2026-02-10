<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register route middleware aliases
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'purok_leader_only' => \App\Http\Middleware\PurokLeaderMiddleware::class,
            'barangay_official' => \App\Http\Middleware\BarangayOfficialMiddleware::class,
            'checkrole' => \App\Http\Middleware\CheckRole::class,
            'resident_approved' => \App\Http\Middleware\CheckResidentApproved::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
        ]);
        
        // Register web middleware group
        $middleware->web(append: [
            \App\Http\Middleware\CheckFeedbackEligibility::class,
            \App\Http\Middleware\CheckForPendingFeedback::class,
            // CheckResidentApproved is applied to specific routes in web.php, not globally
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
