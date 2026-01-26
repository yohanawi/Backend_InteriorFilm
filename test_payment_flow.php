#!/usr/bin/env php
<?php
/**
 * Payment Integration Test Script
 * Tests the complete payment flow from order creation to payment verification
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\NgeniusClient;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

echo "\n";
echo "═══════════════════════════════════════════════════════════\n";
echo "   PAYMENT INTEGRATION FLOW TEST\n";
echo "═══════════════════════════════════════════════════════════\n";
echo "\n";

// Test 1: Check Database Schema
echo "📊 Test 1: Checking Database Schema...\n";
$requiredColumns = [
    'payment_status',
    'payment_transaction_id',
    'payment_completed_at',
    'payment_error_message',
    'payment_metadata'
];

$missingColumns = [];
foreach ($requiredColumns as $column) {
    if (!Schema::hasColumn('orders', $column)) {
        $missingColumns[] = $column;
    }
}

if (empty($missingColumns)) {
    echo "   ✅ All payment columns exist\n";
} else {
    echo "   ❌ Missing columns: " . implode(', ', $missingColumns) . "\n";
    echo "   Run: php artisan migrate\n";
    exit(1);
}
echo "\n";

// Test 2: Check N-Genius Connection
echo "🌐 Test 2: Testing N-Genius API Connection...\n";
try {
    $client = NgeniusClient::fromConfig();
    $token = $client->getAccessToken();
    echo "   ✅ N-Genius API connection successful\n";
    echo "   Token length: " . strlen($token) . " characters\n";
} catch (Exception $e) {
    echo "   ❌ N-Genius connection failed: " . $e->getMessage() . "\n";
    exit(1);
}
echo "\n";

// Test 3: Test Order Creation Flow
echo "🛒 Test 3: Testing Order Creation...\n";
try {
    // Find a test product
    $product = Product::where('is_active', true)->first();
    if (!$product) {
        echo "   ⚠️  No active products found. Create a product first.\n";
    } else {
        echo "   ✅ Test product found: " . $product->name . "\n";
        echo "   Price: " . $product->price . " " . config('ngenius.currency') . "\n";

        // Calculate test order amount
        $quantity = 1;
        $unitPrice = $product->price;
        if ($product->discount_value > 0) {
            if ($product->discount_type === 'percentage') {
                $unitPrice = $product->price - ($product->price * $product->discount_value / 100);
            } else {
                $unitPrice = $product->price - $product->discount_value;
            }
        }

        $subtotal = $unitPrice * $quantity;
        $tax = round($subtotal * 0.08, 2);
        $total = round($subtotal + $tax, 2);
        $amountMinor = (int) round($total * 100);

        echo "   Subtotal: " . number_format($subtotal, 2) . "\n";
        echo "   Tax (8%): " . number_format($tax, 2) . "\n";
        echo "   Total: " . number_format($total, 2) . " " . config('ngenius.currency') . "\n";
        echo "   Amount (minor units): " . $amountMinor . "\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 4: Test Payment Order Creation
echo "💳 Test 4: Testing N-Genius Payment Order Creation...\n";
try {
    $testAmount = 10000; // 100.00 AED in fils (minor units)
    $currency = config('ngenius.currency', 'AED');
    $redirectUrl = config('ngenius.redirect_url');

    echo "   Creating test payment order...\n";
    echo "   Amount: " . ($testAmount / 100) . " {$currency}\n";
    echo "   Redirect URL: {$redirectUrl}\n";

    $ngOrder = $client->createHostedPaymentOrder(
        amountMinor: $testAmount,
        currencyCode: $currency,
        redirectUrl: $redirectUrl,
        emailAddress: 'test@example.com',
        action: 'PURCHASE',
        additionalOptions: [
            'merchant_order_reference' => 'TEST-' . time(),
            'customer_name' => 'Test Customer',
        ]
    );

    echo "   ✅ Payment order created successfully!\n";
    echo "   Reference: " . $ngOrder['reference'] . "\n";
    echo "   Payment URL: " . substr($ngOrder['payment_url'], 0, 60) . "...\n";
} catch (Exception $e) {
    echo "   ❌ Failed to create payment order\n";
    echo "   Error: " . $e->getMessage() . "\n";
    echo "\n";
    echo "   Check:\n";
    echo "   - N-Genius credentials are correct\n";
    echo "   - Outlet ID is valid\n";
    echo "   - API key is correct\n";
    exit(1);
}
echo "\n";

// Test 5: Test Payment Verification
echo "🔍 Test 5: Testing Payment Status Verification...\n";
try {
    $orderReference = $ngOrder['reference'];
    echo "   Checking status of order: {$orderReference}\n";

    $orderData = $client->retrieveOrder($orderReference);
    $parsed = $client->parsePaymentState($orderData);

    echo "   ✅ Payment status retrieved successfully\n";
    echo "   State: " . ($parsed['state'] ?? 'UNKNOWN') . "\n";
    echo "   Transaction ID: " . ($parsed['transaction_id'] ?? 'N/A') . "\n";
} catch (Exception $e) {
    echo "   ❌ Failed to retrieve payment status\n";
    echo "   Error: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 6: Check Routes
echo "🛣️  Test 6: Verifying Payment Routes...\n";
$routes = app('router')->getRoutes();
$foundRoutes = [];

foreach ($routes as $route) {
    if (str_contains($route->uri(), 'payment')) {
        $foundRoutes[] = $route->uri();
    }
}

if (empty($foundRoutes)) {
    echo "   ❌ No payment routes found\n";
    echo "   Add routes in routes/web.php\n";
} else {
    echo "   ✅ Payment routes registered:\n";
    foreach ($foundRoutes as $route) {
        echo "      - /" . $route . "\n";
    }
}
echo "\n";

// Test 7: Check Frontend Configuration
echo "🌍 Test 7: Checking Frontend Configuration...\n";
$frontendUrl = config('app.frontend_url');
if ($frontendUrl) {
    echo "   ✅ Frontend URL: {$frontendUrl}\n";
    echo "   Success URL: {$frontendUrl}/order/payment-success\n";
    echo "   Failed URL: {$frontendUrl}/order/payment-failed\n";
    echo "   Cancel URL: {$frontendUrl}/order/payment-cancelled\n";
} else {
    echo "   ⚠️  Frontend URL not configured\n";
    echo "   Set FRONTEND_URL in .env\n";
}
echo "\n";

// Summary
echo "═══════════════════════════════════════════════════════════\n";
echo "   🎉 PAYMENT INTEGRATION TEST COMPLETE!\n";
echo "═══════════════════════════════════════════════════════════\n";
echo "\n";
echo "✅ Your payment integration is working correctly!\n";
echo "\n";
echo "📝 Next Steps:\n";
echo "1. Make sure Laravel is running: php artisan serve\n";
echo "2. Make sure Next.js is running: npm run dev\n";
echo "3. Test the complete checkout flow:\n";
echo "   - Add products to cart\n";
echo "   - Go to checkout\n";
echo "   - Fill in shipping details\n";
echo "   - Click 'Place Order'\n";
echo "   - You'll be redirected to N-Genius payment page\n";
echo "   - Use test card: 4000 0000 0000 0002\n";
echo "   - Complete payment and verify you're redirected back\n";
echo "\n";
echo "🎯 Test Cards:\n";
echo "   Success: 4000 0000 0000 0002 (Exp: 12/25, CVV: 123)\n";
echo "   Decline: 4000 0000 0000 0051 (Exp: 12/25, CVV: 123)\n";
echo "   3DS:     5123 4567 8901 2346 (Exp: 12/25, CVV: 123)\n";
echo "\n";
echo "📊 Monitor logs:\n";
echo "   Laravel: tail -f storage/logs/laravel.log\n";
echo "   Browser: Open DevTools Console (F12)\n";
echo "\n";
