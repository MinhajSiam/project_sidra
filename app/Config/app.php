<?php

declare(strict_types=1);

return [
    'name' => $_ENV['APP_NAME'] ?? 'Sidra Event Platform',
    'env' => $_ENV['APP_ENV'] ?? 'production',
    'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
    'url' => rtrim($_ENV['APP_URL'] ?? 'http://localhost:8000', '/'),
    'key' => $_ENV['APP_KEY'] ?? '32_character_random_secret_encryption_key',
    'ticket_secret_key' => $_ENV['TICKET_SECRET_KEY'] ?? 'sidra_default_ticket_verification_secret_key',
    'session' => [
        'lifetime' => (int)($_ENV['SESSION_LIFETIME'] ?? 7200),
        'secure' => filter_var($_ENV['SESSION_SECURE_COOKIE'] ?? false, FILTER_VALIDATE_BOOLEAN),
        'name' => 'SIDRA_SESSION',
    ],
    'locale' => [
        'currency' => $_ENV['DEFAULT_CURRENCY'] ?? 'BDT',
        'currency_symbol' => $_ENV['DEFAULT_CURRENCY_SYMBOL'] ?? '৳',
        'timezone' => 'Asia/Dhaka',
    ],
    'limits' => [
        'max_tickets_per_order' => (int)($_ENV['MAX_TICKETS_PER_ORDER'] ?? 10),
        'max_upload_size' => 5 * 1024 * 1024, // 5MB
    ],
];
