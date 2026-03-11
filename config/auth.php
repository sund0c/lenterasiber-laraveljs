<?php

return [
    'defaults' => [
        'guard'     => 'web',
        'passwords' => 'users',
    ],

    'guards' => [
        'web' => [
            'driver'   => 'session',
            'provider' => 'users',
        ],
        'admin' => [
            'driver'   => 'session',
            'provider' => 'admin_users',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model'  => App\Models\AdminUser::class,
        ],
        'admin_users' => [
            'driver' => 'eloquent',
            'model'  => App\Models\AdminUser::class,
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table'    => 'password_reset_tokens',
            'expire'   => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,

    // Custom: login attempt limits
    'max_attempts'     => env('AUTH_MAX_ATTEMPTS', 5),
    'lockout_minutes'  => env('AUTH_LOCKOUT_MINUTES', 15),
];
