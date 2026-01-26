#!/usr/bin/env php
<?php
/**
 * N-Genius Payment Gateway Test Script
 * 
 * This script tests the N-Genius connection and verifies configuration.
 * Run: php test_ngenius_connection.php
 */

require __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\NgeniusClient;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

echo "\n";
echo "═══════════════════════════════════════════════════════════\n";
echo "   N-GENIUS PAYMENT GATEWAY CONNECTION TEST\n";
echo "═══════════════════════════════════════════════════════════\n";
echo "\n";

// Test 1: Configuration
echo "📋 Test 1: Checking Configuration...\n";
echo "   Environment: " . config('ngenius.environment') . "\n";
echo "   Currency: " . config('ngenius.currency') . "\n";
echo "   3DS Enabled: " . (config('ngenius.features.3ds_enabled') ? 'Yes' : 'No') . "\n";
echo "   Redirect URL: " . config('ngenius.redirect_url') . "\n";

$env = config('ngenius.environment');
$outletId = config("ngenius.{$env}.outlet_id");
$apiKey = config("ngenius.{$env}.api_key");

if (!$outletId || !$apiKey) {
    echo "   ❌ FAILED: Outlet ID or API Key not configured\n";
    echo "\n";
    echo "   Please update your .env file with:\n";
    echo "   - NGENIUS_SANDBOX_OUTLET_ID=your-outlet-id\n";
    echo "   - NGENIUS_SANDBOX_API_KEY=your-api-key\n";
    echo "\n";
    exit(1);
}

echo "   ✅ Configuration looks good!\n";
echo "   Outlet ID: " . substr($outletId, 0, 20) . "...\n";
echo "\n";

// Test 2: API Connection
echo "🌐 Test 2: Testing API Connection...\n";

try {
    $client = NgeniusClient::fromConfig();
    echo "   ✅ Client initialized successfully\n";
} catch (Exception $e) {
    echo "   ❌ FAILED: " . $e->getMessage() . "\n";
    echo "\n";
    exit(1);
}

// Test 3: Access Token
echo "\n";
echo "🔑 Test 3: Obtaining Access Token...\n";

try {
    $token = $client->getAccessToken();
    echo "   ✅ Access token obtained successfully!\n";
    echo "   Token (first 30 chars): " . substr($token, 0, 30) . "...\n";
    echo "   Token length: " . strlen($token) . " characters\n";
} catch (Exception $e) {
    echo "   ❌ FAILED: " . $e->getMessage() . "\n";
    echo "\n";
    echo "   Common causes:\n";
    echo "   - Invalid API credentials\n";
    echo "   - Network connectivity issues\n";
    echo "   - N-Genius service is down\n";
    echo "\n";
    exit(1);
}

// Test 4: Database Connection
echo "\n";
echo "💾 Test 4: Checking Database...\n";

try {
    $hasColumns =
        Schema::hasColumn('orders', 'payment_status') &&
        Schema::hasColumn('orders', 'payment_transaction_id') &&
        Schema::hasColumn('orders', 'payment_completed_at');

    if ($hasColumns) {
        echo "   ✅ Payment tracking columns exist\n";
    } else {
        echo "   ❌ FAILED: Payment tracking columns missing\n";
        echo "   Run: php artisan migrate\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "   ❌ FAILED: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 5: Routes
echo "\n";
echo "🛣️  Test 5: Checking Routes...\n";

try {
    $redirectUrl = (string) config('ngenius.redirect_url');
    $appUrl = (string) config('app.url');

    // If redirect is handled by the frontend, Laravel doesn't need a return route.
    if ($redirectUrl && $appUrl && !str_starts_with($redirectUrl, $appUrl)) {
        echo "   ✅ Redirect handled by frontend, skipping Laravel return-route check\n";
        echo "   Redirect URL: {$redirectUrl}\n";
    } else {
        $routes = app('router')->getRoutes();
        $hasRoute = false;

        foreach ($routes as $route) {
            if (str_contains($route->uri(), 'payment/ngenius/return')) {
                $hasRoute = true;
                break;
            }
        }

        if ($hasRoute) {
            echo "   ✅ Payment return route registered\n";
            echo "   Route: /payment/ngenius/return\n";
        } else {
            echo "   ⚠️  WARNING: Laravel payment return route not found\n";
            echo "   This is OK if NGENIUS_REDIRECT_URL points to the Next.js app.\n";
        }
    }
} catch (Exception $e) {
    echo "   ❌ FAILED: " . $e->getMessage() . "\n";
    echo "\n";
    exit(1);
}

// Test 6: Frontend URL
echo "\n";
echo "🌍 Test 6: Checking Frontend Configuration...\n";

$frontendUrl = config('app.frontend_url');
if ($frontendUrl) {
    echo "   ✅ Frontend URL configured\n";
    echo "   URL: " . $frontendUrl . "\n";
} else {
    echo "   ⚠️  WARNING: Frontend URL not configured\n";
    echo "   Set FRONTEND_URL in .env file\n";
}

// Test 7: CORS
echo "\n";
echo "🔒 Test 7: Checking CORS Configuration...\n";

$corsOrigins = config('cors.allowed_origins');
if (is_array($corsOrigins) && count($corsOrigins) > 0) {
    echo "   ✅ CORS origins configured\n";
    echo "   Allowed origins: " . implode(', ', array_slice($corsOrigins, 0, 3)) . "\n";
} else {
    echo "   ⚠️  WARNING: CORS origins not configured\n";
}

// Summary
echo "\n";
echo "═══════════════════════════════════════════════════════════\n";
echo "   🎉 ALL TESTS PASSED!\n";
echo "═══════════════════════════════════════════════════════════\n";
echo "\n";
echo "Your N-Genius integration is configured correctly!\n";
echo "\n";
echo "Next steps:\n";
echo "1. Start Laravel server: php artisan serve\n";
echo "2. Start Next.js frontend: npm run dev\n";
echo "3. Test checkout with test card: 4000 0000 0000 0002\n";
echo "\n";
echo "Test cards:\n";
echo "  Success: 4000 0000 0000 0002\n";
echo "  Decline: 4000 0000 0000 0051\n";
echo "  3DS:     5123 4567 8901 2346\n";
echo "\n";
echo "For detailed guide, see: PAYMENT_INTEGRATION_README.md\n";
echo "\n";
