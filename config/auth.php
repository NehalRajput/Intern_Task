<?php

return [
    'defaults' => [
        'guard' => 'user',
        'passwords' => 'users',
    ],

    'guards' => [
        'user' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
        'admin' => [
            'driver' =>'session',
            'provider' => 'admins',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => env('AUTH_MODEL', App\Models\User::class),
        ],

        'admins' => [
            'driver' => 'eloquent',
            'model' => env('AUTH_ADMIN_MODEL', App\Models\Admin::class),
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),
];