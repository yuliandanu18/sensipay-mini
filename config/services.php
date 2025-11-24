<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */
    'jet' => [
    'admin_wa'      => env('JET_ADMIN_WA'),
    'admin_wa_text' => env('JET_ADMIN_WA_TEXT', 'Halo admin Bimbel JET, saya ingin menanyakan tagihan.'),
],

    'fonnte' => [
        'token'         => env('FONNTE_TOKEN'),
        'url'           => env('FONNTE_URL', 'https://api.fonnte.com/send'),
        'default_sender'=> env('FONNTE_DEFAULT_SENDER', null),
        'admin_number'  => env('JET_ADMIN_WA'),
    ],

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];
