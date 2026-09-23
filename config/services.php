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
    'midtrans' => [
        'merchant_id' => env('MIDTRANS_MERCHANT_ID'),
        'server_key' => env('MIDTRANS_SERVER_KEY'),
        'client_key' => env('MIDTRANS_CLIENT_KEY'),
        'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
        // Kept separate from payment credentials. The payout product must be
        // activated and its official contract supplied by Midtrans first.
        'payout_enabled' => env('MIDTRANS_PAYOUT_ENABLED', false),
        'payout_mode' => env('MIDTRANS_PAYOUT_MODE', 'simulation'),
        'payout_key' => env('MIDTRANS_PAYOUT_KEY'),
        'payout_endpoint' => env('MIDTRANS_PAYOUT_ENDPOINT'),
        'payout_callback_token' => env('MIDTRANS_PAYOUT_CALLBACK_TOKEN'),
        'payout_simulation_status' => env('MIDTRANS_PAYOUT_SIMULATION_STATUS', 'paid'),
    ],

    'midtrans_bi_snap' => [
        'base_url' => env('MIDTRANS_BI_SNAP_BASE_URL', 'https://merchants.sbx.midtrans.com'),
        // Kept separate so BI-SNAP can be configured independently; defaults
        // to the existing MID without changing legacy payment credentials.
        'merchant_id' => env('MIDTRANS_BI_SNAP_MERCHANT_ID', env('MIDTRANS_MERCHANT_ID')),
        'client_id' => env('MIDTRANS_BI_SNAP_CLIENT_ID'),
        // Reserved for BI-SNAP transactional APIs; it is not sent to the
        // Access Token API.
        'client_secret' => env('MIDTRANS_BI_SNAP_CLIENT_SECRET'),
        'merchant_public_key_path' => env('MIDTRANS_BI_SNAP_MERCHANT_PUBLIC_KEY_PATH'),
        'private_key_path' => env('MIDTRANS_BI_SNAP_PRIVATE_KEY_PATH'),
        // Required only by BI-SNAP transactional APIs, not access-token/b2b.
        'partner_id' => env('MIDTRANS_BI_SNAP_PARTNER_ID'),
    ],

    'recaptcha' => [
        'site_key' => env('RECAPTCHA_SITE_KEY'),
        'secret_key' => env('RECAPTCHA_SECRET_KEY'),
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
