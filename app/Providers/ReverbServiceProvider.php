<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

class ReverbServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force the configuration
        config([
            'broadcasting.connections.reverb' => [
                'driver' => 'reverb',
                'key' => 'kalawag_brgy_key',
                'secret' => 'kalawag_brgy_secret',
                'app_id' => 'kalawag_brgy_system',
                'options' => [
                    'host' => '127.0.0.1',
                    'port' => 8080,
                    'scheme' => 'http',
                    'useTLS' => false,
                ],
                'client_options' => [
                    'verify' => false,
                    'curl' => [
                        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                    ],
                ],
            ]
        ]);
    }
}
