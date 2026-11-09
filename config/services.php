<?php

return [
    'metadata_provider' => env('METADATA_PROVIDER', env('GOOGLE_BOOKS_API_KEY') ? 'google_books' : 'openlibrary'),
    'openlibrary' => [
        'ca_bundle' => env('OPENLIBRARY_CA_BUNDLE') ? base_path(env('OPENLIBRARY_CA_BUNDLE')) : true,
        'base_url' => env('OPENLIBRARY_BASE_URL', 'https://openlibrary.org'),
        'timeout' => (int) env('OPENLIBRARY_TIMEOUT', 4),
        'cache_seconds' => (int) env('OPENLIBRARY_CACHE_SECONDS', 3600),
        'user_agent' => env('OPENLIBRARY_USER_AGENT', 'PerpustakaanKelompok2/1.1 (student project)'),
    ],
    'google_books' => [
        'key' => env('GOOGLE_BOOKS_API_KEY'),
        'base_url' => env('GOOGLE_BOOKS_BASE_URL', 'https://www.googleapis.com/books/v1'),
        'timeout' => (int) env('GOOGLE_BOOKS_TIMEOUT', 6),
        'cache_seconds' => (int) env('GOOGLE_BOOKS_CACHE_SECONDS', 3600),
        'ca_bundle' => env('GOOGLE_BOOKS_CA_BUNDLE', file_exists('C:/xampp/apache/bin/curl-ca-bundle.crt') ? 'C:/xampp/apache/bin/curl-ca-bundle.crt' : true),
    ],
    'demo' => ['password' => env('DEMO_PASSWORD')],

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

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
