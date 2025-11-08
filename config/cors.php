<?php

return [
    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
        'cors-check', // tambahkan endpoint untuk pengecekan CORS
        '*',
    ],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'http://localhost:5173', // untuk dev React lokal
        'https://chill-ajar.vercel.app',
        'https://chill-ajar-pentest.vercel.app',
        'https://manpro-sizzlingchilli-backend-chill-ajar.onrender.com',
        'https://35.188.34.254', // akses langsung ke IP server
        'https://chillajar.my.id', // akses langsung ke domain frontend
        'https://peladen.my.id', // akses langsung ke domain backend
        'http://ekomh29.biz.id', // domain baru untuk deployment
        'https://ekomh29.biz.id' // domain baru dengan HTTPS
    ],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
