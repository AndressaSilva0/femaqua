<?php

return [

    'defaults' => [
        'guard' => 'api', // API com JWT como padrão
        'passwords' => 'users',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session', // Para acesso via navegador (ex: login via formulário)
            'provider' => 'users',
        ],

        'api' => [
            'driver' => 'jwt',
            'provider' => 'users',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],


    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800, 
];
