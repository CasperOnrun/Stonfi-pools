<?php

return [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'STON.fi Pools Dashboard',
    'stonfi' => [
        'apiUrl' => getenv('STONFI_API_URL') ?: 'https://rpc.ston.fi/',
    ],
];

