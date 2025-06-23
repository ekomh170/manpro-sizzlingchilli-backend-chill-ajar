<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', '*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'http://localhost:5173', // untuk dev React lokal
        'https://chill-ajar.vercel.app',
        'https://chill-ajar-pentest.vercel.app',
        'https://manpro-sizzlingchilli-backend-chill-ajar.onrender.com',
        'http://35.188.34.254', // akses langsung ke IP server
        'http://chillajar.biz.id', // akses langsung ke domain
    ],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
