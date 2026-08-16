<?php

return [
    'name' => 'MediCore Pharmacy System',
    'env' => getenv('APP_ENV') ?: 'development',
    'debug' => getenv('APP_DEBUG') === 'true',
    'url' => getenv('APP_URL') ?: 'http://localhost:8000',
    'timezone' => 'Asia/Jakarta',
    'locale' => 'id',
    'jwt_secret' => getenv('JWT_SECRET') ?: 'your-secret-key-change-in-production',
    'jwt_expiry' => 3600,
    'log_path' => __DIR__ . '/../storage/logs',
    'session' => [
        'lifetime' => 7200, // 2 hours in seconds
        'expire_on_close' => false,
        'cookie' => 'medicore_session',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'lax'
    ]
];