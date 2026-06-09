<?php

return [
    'adminEmail' => 'admin@example.com',
    'telegram' => [
        'botToken' => getenv('TELEGRAM_BOT_TOKEN'),
        'webAppUrl' => getenv('TELEGRAM_WEBAPP_URL') ?: 'https://localhost/tma.html',
        'mtproto' => [
            'app_id' => getenv('MTPROTO_APP_ID'),
            'app_hash' => getenv('MTPROTO_APP_HASH'),
        ],
    ]
];
