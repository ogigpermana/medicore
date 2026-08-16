<?php

return [
    'host' => getenv('MAIL_HOST') ?: 'smtp.gmail.com',
    'port' => getenv('MAIL_PORT') ?: 587,
    'username' => getenv('MAIL_USERNAME') ?: '',
    'password' => getenv('MAIL_PASSWORD') ?: '',
    'encryption' => getenv('MAIL_ENCRYPTION') ?: 'tls',
    'from_email' => getenv('MAIL_FROM_EMAIL') ?: 'noreply@medicore.com',
    'from_name' => getenv('MAIL_FROM_NAME') ?: 'MediCore Pharmacy',
    'reply_to' => getenv('MAIL_REPLY_TO') ?: 'noreply@medicore.com',
    'reply_to_name' => getenv('MAIL_REPLY_TO_NAME') ?: 'MediCore Support',
    'debug' => (getenv('MAIL_DEBUG') ?: 'false') === 'true' ? 2 : 0,
];