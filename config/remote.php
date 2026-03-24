<?php

return [

    'default_connection' => env('REMOTE_DEFAULT_CONNECTION', 'production'),

    'connections' => [
        'production' => [
            'host' => env('REMOTE_HOST'),
            'username' => env('REMOTE_USER'),
            'port' => env('REMOTE_PORT', 22),
            'private_key' => env('REMOTE_PRIVATE_KEY_PATH'),
            'needs_confirmation' => env('REMOTE_NEEDS_CONFIRMATION', false),
        ],
    ],

    'php_path' => env('REMOTE_PHP_PATH', 'php'),
];