<?php

return [
    'default' => 'reverb',

    'servers' => [
        'reverb' => [
            'host' => '0.0.0.0',
            'port' => 8080,
            'path' => '',
            'hostname' => '127.0.0.1',
            'options' => [
                'tls' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
            ],
            'max_request_size' => 10000,
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
            'pulse_ingest_interval' => 15,
            'telescope_ingest_interval' => 15,
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
                'allowed_origins' => ['*'],
                'ping_interval' => 60,
                'activity_timeout' => 30,
                'max_message_size' => 10000,
            ],
        ],
    ],
];
