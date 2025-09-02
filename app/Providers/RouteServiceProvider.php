<?php

namespace App\Providers;

use App\Models\Request as RequestModel;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/dashboard';

    public function boot(): void
    {
        parent::boot();

        // Explicit route model bindings
        Route::model('request', RequestModel::class);
        
        // Custom binding for requests
        Route::bind('request', function ($value) {
            return RequestModel::findOrFail($value);
        });
        
        // Handle purok_leader route parameter to prevent binding errors
        Route::bind('purok_leader', function ($value) {
            return \App\Models\User::where('role', 'purok_leader')
                                 ->findOrFail($value);
        });

        Route::middleware('web')
            ->group(base_path('routes/web.php'));

        Route::middleware('api')
            ->prefix('api')
            ->group(base_path('routes/api.php'));
    }
}
