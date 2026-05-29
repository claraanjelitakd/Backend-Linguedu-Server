<?php

return [

    'defaults' => [
        'guard' => 'member', // default bisa diganti sesuai kebutuhan
        'passwords' => 'member',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    */

    'guards' => [
        'admin' => [
            'driver' => 'jwt',
            'provider' => 'admin',
        ],

        'member' => [
            'driver' => 'jwt',
            'provider' => 'member',
        ],

        // Optional: session login (untuk testing di laravel UI jika perlu)
        'web' => [
            'driver' => 'session',
            'provider' => 'member',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    */

    'providers' => [
        'admin' => [
            'driver' => 'eloquent',
            'model' => App\Models\Admin::class,
        ],

        'member' => [
            'driver' => 'eloquent',
            'model' => App\Models\Member::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Reset Config
    |--------------------------------------------------------------------------
    */

    'passwords' => [
        'admin' => [
            'provider' => 'admin',
            'table' => 'password_reset_tokens',
            'expire' => 60,
        ],

        'member' => [
            'provider' => 'member',
            'table' => 'password_reset_tokens',
            'expire' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Timeout Settings
    |--------------------------------------------------------------------------
    */

    'password_timeout' => 10800,

];
