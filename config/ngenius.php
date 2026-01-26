<?php

/**
 * N-Genius Hosted Payment Page (HPP) configuration.
 *
 * This file is intentionally compatible with BOTH:
 * - the newer env layout in `.env.ngenius.example` (NGENIUS_SANDBOX_* / NGENIUS_PRODUCTION_*)
 * - the older simplified env layout (NGENIUS_OUTLET_ID / NGENIUS_API_KEY)
 */

$environmentRaw = strtolower((string) env('NGENIUS_ENV', 'sandbox'));
$environment = in_array($environmentRaw, ['production', 'prod', 'live'], true) ? 'production' : 'sandbox';
$envPrefix = $environment === 'production' ? 'NGENIUS_PRODUCTION' : 'NGENIUS_SANDBOX';

$sandboxUrl = (string) env('NGENIUS_SANDBOX_URL', 'https://api-gateway.sandbox.ngenius-payments.com');
$productionUrl = (string) env('NGENIUS_PRODUCTION_URL', 'https://api-gateway.ngenius-payments.com');

$outletId = (string) (
    env("{$envPrefix}_OUTLET_ID")
    ?? env('NGENIUS_OUTLET_ID')
    ?? ''
);

$apiKey = (string) (
    env("{$envPrefix}_API_KEY")
    ?? env('NGENIUS_API_KEY')
    ?? ''
);

$realmName = (string) (
    env("{$envPrefix}_REALM")
    ?? env('NGENIUS_REALM')
    ?? 'ni'
);

$normalizeUrl = static function (string $url): string {
    $url = trim($url);
    if ($url === '') {
        return '';
    }

    // Collapse duplicate slashes in the path while preserving the scheme (https://).
    return (string) preg_replace('#(?<!:)//+#', '/', $url);
};

$appUrl = $normalizeUrl(rtrim((string) env('APP_URL', ''), '/'));

$redirectUrl = $normalizeUrl((string) env('NGENIUS_REDIRECT_URL', $appUrl . '/order/ngenius-return'));
$cancelUrl = $normalizeUrl((string) env('NGENIUS_CANCEL_URL', $appUrl . '/order/payment-cancel'));

return [
    // Environment
    'environment' => $environment,
    'sandbox_url' => $sandboxUrl,
    'production_url' => $productionUrl,

    // Credentials (env-specific)
    'sandbox' => [
        'outlet_id' => (string) env('NGENIUS_SANDBOX_OUTLET_ID', ''),
        'api_key' => (string) env('NGENIUS_SANDBOX_API_KEY', ''),
        'realm_name' => (string) env('NGENIUS_SANDBOX_REALM', 'ni'),
    ],
    'production' => [
        'outlet_id' => (string) env('NGENIUS_PRODUCTION_OUTLET_ID', ''),
        'api_key' => (string) env('NGENIUS_PRODUCTION_API_KEY', ''),
        'realm_name' => (string) env('NGENIUS_PRODUCTION_REALM', 'ni'),
    ],

    // Convenience fields used by older code/tests
    'api_url' => $environment === 'production' ? $productionUrl : $sandboxUrl,
    'outlet_id' => $outletId,
    'api_key' => $apiKey,
    'realm_name' => $realmName,

    // Payment options
    'currency' => (string) env('NGENIUS_CURRENCY', 'AED'),
    'language' => (string) env('NGENIUS_LANGUAGE', 'en'),
    'default_action' => (string) env('NGENIUS_DEFAULT_ACTION', 'PURCHASE'),

    // Redirects
    'redirect_url' => $redirectUrl,
    'cancel_url' => $cancelUrl,
    'success_url' => $normalizeUrl((string) env('NGENIUS_SUCCESS_URL', $appUrl . '/order/payment-success')),

    // Webhooks
    'webhook_url' => $normalizeUrl((string) env('NGENIUS_WEBHOOK_URL', $appUrl . '/api/webhooks/ngenius')),
    'webhook_secret' => (string) env('NGENIUS_WEBHOOK_SECRET', ''),

    // Timeouts/retries
    'timeout' => (int) env('NGENIUS_TIMEOUT', 30),
    'max_retries' => (int) env('NGENIUS_MAX_RETRIES', 3),
    'token_cache_ttl' => (int) env('NGENIUS_TOKEN_CACHE_TTL', 240),
    'order_ttl' => (int) env('NGENIUS_ORDER_TTL', 3600),

    // Feature toggles
    'features' => [
        '3ds_enabled' => filter_var(env('NGENIUS_3DS_ENABLED', true), FILTER_VALIDATE_BOOL),
        'tokenization_enabled' => filter_var(env('NGENIUS_TOKENIZATION_ENABLED', false), FILTER_VALIDATE_BOOL),
        'slim_mode' => filter_var(env('NGENIUS_SLIM_MODE', false), FILTER_VALIDATE_BOOL),
        'mask_card_details' => filter_var(env('NGENIUS_MASK_CARD', true), FILTER_VALIDATE_BOOL),
        'prepopulate_cardholder_name' => filter_var(env('NGENIUS_PREPOPULATE_NAME', true), FILTER_VALIDATE_BOOL),
    ],

    'payment_methods' => [
        'cards' => filter_var(env('NGENIUS_ALLOW_CARDS', true), FILTER_VALIDATE_BOOL),
        'apple_pay' => filter_var(env('NGENIUS_ALLOW_APPLE_PAY', false), FILTER_VALIDATE_BOOL),
        'samsung_pay' => filter_var(env('NGENIUS_ALLOW_SAMSUNG_PAY', false), FILTER_VALIDATE_BOOL),
        'google_pay' => filter_var(env('NGENIUS_ALLOW_GOOGLE_PAY', false), FILTER_VALIDATE_BOOL),
    ],

    // Logging
    'log_requests' => filter_var(env('NGENIUS_LOG_REQUESTS', true), FILTER_VALIDATE_BOOL),
    'log_responses' => filter_var(env('NGENIUS_LOG_RESPONSES', true), FILTER_VALIDATE_BOOL),
];
