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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_CALLBACK_URL'),
    ],

    'facebook' => [
        'client_id'     => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect'      => '/auth/redirect/facebook',
    ],

    'ngenius' => [
        // Example (Sandbox): https://api-gateway.sandbox.ngenius-payments.com
        'base_url' => env('NGENIUS_BASE_URL', 'https://api-gateway.sandbox.ngenius-payments.com'),
        'outlet_id' => env('NGENIUS_OUTLET_ID'),
        // Service Account API key (Base64) used as: Authorization: Basic <api_key>
        'api_key' => env('NGENIUS_API_KEY'),
        'realm_name' => env('NGENIUS_REALM_NAME', 'ni'),

        // Defaults used when creating HPP orders
        'currency' => env('NGENIUS_CURRENCY', 'AED'),
        // Full URL on the Next.js site where N-Genius will redirect after payment.
        // N-Genius will append `ref=<orderReference>` as a query parameter.
        'redirect_url_base' => env('NGENIUS_REDIRECT_URL_BASE'),
    ],
];
