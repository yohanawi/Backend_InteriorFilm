<?php

/**
 * Test N-Genius Minimal Payment Flow
 * 
 * Tests the simplified NgeniusClient with minimal payload
 */

require __DIR__ . '/vendor/autoload.php';

use App\Services\NgeniusClient;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "========================================\n";
echo "N-GENIUS MINIMAL PAYMENT FLOW TEST\n";
echo "========================================\n\n";

// Test 1: Configuration
echo "Test 1: Configuration Check\n";
$apiUrl = config('ngenius.api_url');
$outletId = config('ngenius.outlet_id');
$apiKey = config('ngenius.api_key');
$currency = config('ngenius.currency');
$redirectUrl = config('ngenius.redirect_url');

echo "API URL: " . $apiUrl . "\n";
echo "Outlet ID: " . $outletId . "\n";
echo "API Key: " . substr($apiKey, 0, 20) . "...\n";
echo "Currency: " . $currency . "\n";
echo "Redirect URL: " . $redirectUrl . "\n";

if (empty($apiUrl) || empty($outletId) || empty($apiKey)) {
    die("❌ FAILED: N-Genius configuration missing\n");
}
echo "✅ PASSED\n\n";

// Test 2: Get Access Token
echo "Test 2: Get Access Token\n";
try {
    $client = NgeniusClient::fromConfig();
    $token = $client->getAccessToken();

    if (strlen($token) > 0) {
        echo "Token length: " . strlen($token) . " characters\n";
        echo "✅ PASSED\n\n";
    } else {
        echo "❌ FAILED: Empty token\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "❌ FAILED: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 3: Create Payment Order (Minimal Payload)
echo "Test 3: Create Payment Order (Minimal Payload)\n";
try {
    $testAmount = 10000; // 100 AED in fils
    $testOrderRef = 'TEST-ORDER-' . time();

    echo "Amount: {$testAmount} fils (100 AED)\n";
    echo "Currency: {$currency}\n";
    echo "Order Reference: {$testOrderRef}\n";
    echo "Redirect URL: {$redirectUrl}\n";
    echo "Email: test@example.com\n\n";

    echo "Sending request to N-Genius...\n";

    $result = $client->createOrder(
        amountMinor: $testAmount,
        currencyCode: $currency,
        redirectUrl: $redirectUrl,
        emailAddress: 'test@example.com',
        orderReference: $testOrderRef
    );

    echo "\nResponse received:\n";
    echo "Reference: " . $result['reference'] . "\n";
    echo "Order ID: " . $result['order_id'] . "\n";
    echo "Payment URL: " . substr($result['payment_url'], 0, 50) . "...\n";
    echo "\n✅ PASSED\n\n";

    echo "========================================\n";
    echo "ALL TESTS PASSED! ✅\n";
    echo "========================================\n\n";

    echo "Payment URL (full):\n";
    echo $result['payment_url'] . "\n\n";

    echo "You can test the payment by visiting the URL above.\n";
    echo "Test Card: 4000 0000 0000 0002\n";
    echo "CVV: 123\n";
    echo "Expiry: Any future date\n";
} catch (Exception $e) {
    echo "❌ FAILED: " . $e->getMessage() . "\n\n";

    $msg = $e->getMessage();

    if (str_contains($msg, '422') && str_contains($msg, 'invalidRedirectUrl')) {
        echo "⚠️ NOTE: N-Genius rejected the redirect URL as invalid.\n";
        echo "Make sure it is a valid https URL with no double slashes, e.g.\n";
        echo "  https://interiorfilm.com/order/ngenius-return\n\n";
        echo "Also check the N-Genius dashboard for any redirect URL allowlist settings.\n\n";
    }

    if (str_contains($msg, '422') && str_contains($msg, 'noPaymentMethodsAvailableForCurrency')) {
        echo "⚠️ NOTE: No payment methods are enabled for this currency on the outlet.\n";
        echo "This usually means the outlet is not configured for AED (or cards are disabled) in the current environment.\n\n";
        echo "NEXT STEPS:\n";
        echo "1. In N-Genius (sandbox/production matching your API URL), open the outlet/trading unit settings.\n";
        echo "2. Enable CARD payments and ensure the outlet supports currency {$currency}.\n";
        echo "3. If your outlet is configured for a different currency, temporarily set NGENIUS_CURRENCY to that currency and retry.\n\n";
    }

    // Check if it's a 502 error
    if (str_contains($e->getMessage(), '502')) {
        echo "⚠️ NOTE: 502 Bad Gateway error from N-Genius\n";
        echo "This usually means:\n";
        echo "1. The outlet is not properly configured in N-Genius dashboard\n";
        echo "2. The outlet doesn't have payment processing enabled\n";
        echo "3. The N-Genius sandbox service is temporarily unavailable\n\n";
        echo "NEXT STEPS:\n";
        echo "1. Log in to N-Genius dashboard: https://sandbox.ngenius-payments.com/\n";
        echo "2. Check that outlet {$outletId} exists and is active\n";
        echo "3. Verify payment methods are configured for this outlet\n";
        echo "4. Contact N-Genius support if the problem persists\n";
    }

    exit(1);
}
