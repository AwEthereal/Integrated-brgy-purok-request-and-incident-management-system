<?php

return [
    'default' => env('BROADCAST_DRIVER', 'reverb'),
    
    'connections' => [
        'reverb' => [
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
        ],

        'null' => [
            'driver' => 'null',
        ],
    ],
];
