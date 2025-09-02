<?php

return [
    'default' => 'reverb',

    'servers' => [
        'reverb' => [
            'host' => env('REVERB_HOST', '0.0.0.0'),
            'port' => env('REVERB_PORT', 8080),
            'hostname' => env('REVERB_HOSTNAME', '127.0.0.1'),
            'path' => '/ws',
            'options' => [
                'tls' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ],
            'max_request_size' => 10000,
            'max_connections' => 1000,
            'pulse_ingest_interval' => 15,
            'scaling' => [
                'enabled' => false,
                'channel' => 'reverb',
                'server' => [
                    'url' => env('REDIS_URL'),
                    'host' => env('REDIS_HOST', '127.0.0.1'),
                    'port' => env('REDIS_PORT', '6379'),
                    'username' => env('REDIS_USERNAME'),
                    'password' => env('REDIS_PASSWORD'),
                    'database' => env('REDIS_DB', '0'),
                    'timeout' => env('REDIS_TIMEOUT', 60),
                ],
            ],
        ],
    ],

    'apps' => [
        'provider' => 'config',
        'apps' => [
            [
                'key' => 'kalawag_brgy_key',
                'secret' => 'kalawag_brgy_secret',
                'app_id' => 'kalawag_brgy_system',
                'options' => [
                    'host' => '127.0.0.1',
                    'port' => 8080,
                    'scheme' => 'http',
                    'useTLS' => false,
                ],
                'cors' => [
                    'allow_origins' => [
                        'http://localhost:8000',
                        'http://192.168.1.233:8000',
                        'http://localhost:5173',
                        'http://192.168.1.233:5173'
                    ],
                    'allow_headers' => ['*'],
                    'allow_methods' => ['GET', 'POST', 'OPTIONS'],
                    'allow_credentials' => true,
                    'max_age' => 0,
                ],
                'allowed_origins' => ['*'],
                'ping_interval' => 60,
                'activity_timeout' => 30,
                'max_message_size' => 10000,
            ],
        ],
    ],
];
