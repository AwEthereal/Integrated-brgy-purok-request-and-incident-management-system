<?php

return [
    // Core service providers
    Illuminate\Auth\AuthServiceProvider::class,
    Illuminate\Broadcasting\BroadcastServiceProvider::class,
    
    // Application service providers
    App\Providers\AppServiceProvider::class,
    App\Providers\RouteServiceProvider::class,
    
    // Custom service providers
    App\Providers\ReverbServiceProvider::class,
];
