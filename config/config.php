<?php

return [
    'client_id' => env('MELI_CLIENT_ID'),
    'client_secret' => env('MELI_CLIENT_SECRET'),
    'redirect_url' => env('MELI_REDIRECT_URI'),
    'prefix' => 'api/meli',
    'api' => [
        'state' => md5(env('MELI_CLIENT_ID')),
        'endpoints' => [
            'authorization' => "https://auth.mercadolibre.com.ar/authorization?response_type=code&client_id=APP_ID&redirect_uri=YOUR_URL",
            'base_url' => "https://api.mercadolibre.com",
            'token' => "https://api.mercadolibre.com/oauth/token",
            'me' => "https://api.mercadolibre.com/users/me",
            'create_test_user' => "https://api.mercadolibre.com/users/test_user",
        ],
    ],
];
