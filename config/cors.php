<?php

return [
    'paths' => ['api/*', 'public/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'http://localhost:5173',
        'https://localhost:5173',
        'https://*.vercel.app',
        'https://manpro-sizzlingchilli-frontend.vercel.app', // ganti sesuai domain frontend
        'https://manpro-sizzlingchilli-backend-chill-ajar.onrender.com',
    ],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
