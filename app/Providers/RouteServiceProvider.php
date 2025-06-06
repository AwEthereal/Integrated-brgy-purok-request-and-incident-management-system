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

        // Explicit route model binding
        Route::model('request', RequestModel::class);
        Route::bind('request', function ($value) {
            return RequestModel::findOrFail($value);
        });

        Route::middleware('web')
            ->group(base_path('routes/web.php'));

        Route::middleware('api')
            ->prefix('api')
            ->group(base_path('routes/api.php'));
    }
}
