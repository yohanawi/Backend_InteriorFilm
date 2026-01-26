<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class NgeniusClient
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly string $outletId,
        private readonly string $apiKey,
        private readonly string $realmName,
    ) {}

    public static function fromConfig(): self
    {
        $environment = config('ngenius.environment', 'sandbox');

        // Use new ngenius config if available, fallback to services.ngenius
        if (config('ngenius.environment')) {
            $baseUrl = $environment === 'production'
                ? config('ngenius.production_url')
                : config('ngenius.sandbox_url');

            $credentials = config("ngenius.{$environment}");
            $outletId = (string)($credentials['outlet_id'] ?? '');
            $apiKey = (string)($credentials['api_key'] ?? '');
            $realmName = (string)($credentials['realm_name'] ?? 'ni');
        } else {
            // Fallback to old services.ngenius config
            $cfg = config('services.ngenius');
            $baseUrl = rtrim((string)($cfg['base_url'] ?? ''), '/');
            $outletId = (string)($cfg['outlet_id'] ?? '');
            $apiKey = (string)($cfg['api_key'] ?? '');
            $realmName = (string)($cfg['realm_name'] ?? 'ni');
        }

        if (!$baseUrl || !$outletId || !$apiKey) {
            throw new RuntimeException('N-Genius is not configured. Set NGENIUS_SANDBOX_OUTLET_ID, NGENIUS_SANDBOX_API_KEY in .env');
        }

        return new self($baseUrl, $outletId, $apiKey, $realmName);
    }

    /**
     * N-Genius access token is valid for 5 minutes.
     * Cache slightly less to be safe.
     */
    public function getAccessToken(): string
    {
        $cacheKey = sprintf('ngenius:token:%s:%s', $this->baseUrl, $this->outletId);
        $ttl = config('ngenius.token_cache_ttl', 240);

        return Cache::remember($cacheKey, now()->addSeconds($ttl), function () {
            $url = $this->baseUrl . '/identity/auth/access-token';

            if (config('ngenius.log_requests', true)) {
                Log::info('N-Genius: Requesting access token', ['url' => $url]);
            }

            $res = Http::timeout(config('ngenius.timeout', 30))
                ->accept('application/vnd.ni-identity.v1+json')
                ->withHeaders([
                    'content-type' => 'application/vnd.ni-identity.v1+json',
                    'authorization' => 'Basic ' . $this->apiKey,
                ])
                ->post($url, [
                    'realmName' => $this->realmName,
                ]);

            if (!$res->successful()) {
                Log::error('N-Genius token request failed', [
                    'status' => $res->status(),
                    'body' => $res->body(),
                    'url' => $url,
                ]);
                throw new RuntimeException('Failed to obtain N-Genius access token: ' . $res->status());
            }

            $token = (string)($res->json('access_token') ?? '');
            if (!$token) {
                throw new RuntimeException('N-Genius access token missing in response');
            }

            if (config('ngenius.log_responses', true)) {
                Log::info('N-Genius: Access token obtained successfully');
            }

            return $token;
        });
    }

    /**
     * Create an HPP (Hosted Payment Page) order with advanced features.
     *
     * @return array{reference:string,payment_url:string,raw:array}
     */
    public function createHostedPaymentOrder(
        int $amountMinor,
        string $currencyCode,
        string $redirectUrl,
        ?string $emailAddress = null,
        string $action = 'PURCHASE',
        array $additionalOptions = []
    ): array {
        $token = $this->getAccessToken();

        $url = sprintf('%s/transactions/outlets/%s/orders', $this->baseUrl, $this->outletId);

        // Base payload
        $payload = [
            'action' => $action,
            'amount' => [
                'currencyCode' => $currencyCode,
                'value' => $amountMinor,
            ],
            'merchantAttributes' => [
                'redirectUrl' => $redirectUrl,
            ],
        ];

        // Add email if provided
        if ($emailAddress) {
            $payload['emailAddress'] = $emailAddress;
        }

        // Add language
        if ($language = config('ngenius.language')) {
            $payload['language'] = $language;
        }

        // Add billing address if provided
        if (!empty($additionalOptions['billing_address'])) {
            $payload['billingAddress'] = $additionalOptions['billing_address'];
        }

        // Add shipping address if provided
        if (!empty($additionalOptions['shipping_address'])) {
            $payload['shippingAddress'] = $additionalOptions['shipping_address'];
        }

        // Add customer name for pre-population
        if (config('ngenius.features.prepopulate_cardholder_name') && !empty($additionalOptions['customer_name'])) {
            $payload['merchantDefinedData'] = [
                'cardholderName' => $additionalOptions['customer_name'],
            ];
        }

        // Add order reference/description
        if (!empty($additionalOptions['merchant_order_reference'])) {
            $payload['merchantOrderReference'] = $additionalOptions['merchant_order_reference'];
        }

        // Add payment methods configuration
        if ($allowedMethods = $this->getAllowedPaymentMethods()) {
            $payload['paymentMethods'] = $allowedMethods;
        }

        // Add 3DS configuration
        if (config('ngenius.features.3ds_enabled')) {
            $payload['3ds'] = [
                'required' => true,
            ];
        }

        // Add tokenization if enabled
        if (config('ngenius.features.tokenization_enabled') && !empty($additionalOptions['save_card'])) {
            $payload['merchantAttributes']['saveCard'] = true;
        }

        // Add slim mode if enabled
        if (config('ngenius.features.slim_mode')) {
            $payload['merchantAttributes']['slimMode'] = true;
        }

        // Add card masking if enabled
        if (config('ngenius.features.mask_card_details')) {
            $payload['merchantAttributes']['maskCardDetails'] = true;
        }

        // Add order expiry
        if ($orderTtl = config('ngenius.order_ttl')) {
            $payload['orderExpiry'] = $orderTtl;
        }

        // Log request if enabled
        if (config('ngenius.log_requests', true)) {
            Log::info('N-Genius: Creating order', [
                'url' => $url,
                'amount' => $amountMinor,
                'currency' => $currencyCode,
            ]);
        }

        $res = Http::timeout(config('ngenius.timeout', 30))
            ->accept('application/vnd.ni-payment.v2+json')
            ->withToken($token)
            ->withHeaders([
                'content-type' => 'application/vnd.ni-payment.v2+json',
            ])
            ->post($url, $payload);

        if (!$res->successful()) {
            Log::error('N-Genius create order failed', [
                'status' => $res->status(),
                'body' => $res->body(),
                'url' => $url,
            ]);
            throw new RuntimeException('Failed to create N-Genius order: ' . $res->status());
        }

        $raw = (array) $res->json();
        $reference = (string) (Arr::get($raw, 'reference') ?? '');
        $paymentUrl = (string) (Arr::get($raw, '_links.payment.href') ?? '');

        if (!$reference || !$paymentUrl) {
            Log::error('N-Genius create order response missing data', ['response' => $raw]);
            throw new RuntimeException('N-Genius create order response missing reference/payment URL');
        }

        if (config('ngenius.log_responses', true)) {
            Log::info('N-Genius: Order created successfully', [
                'reference' => $reference,
                'payment_url' => $paymentUrl,
            ]);
        }

        return [
            'reference' => $reference,
            'payment_url' => $paymentUrl,
            'raw' => $raw,
        ];
    }

    /**
     * Get allowed payment methods based on configuration.
     */
    private function getAllowedPaymentMethods(): ?array
    {
        $methods = [];

        $paymentMethods = config('ngenius.payment_methods', []);

        if (!empty($paymentMethods['cards'])) {
            $methods[] = 'CARD';
        }

        if (!empty($paymentMethods['apple_pay'])) {
            $methods[] = 'APPLE_PAY';
        }

        if (!empty($paymentMethods['samsung_pay'])) {
            $methods[] = 'SAMSUNG_PAY';
        }

        if (!empty($paymentMethods['google_pay'])) {
            $methods[] = 'GOOGLE_PAY';
        }

        return empty($methods) ? null : $methods;
    }

    /**
     * Retrieve an order status from N-Genius.
     *
     * @return array raw order object
     */
    public function retrieveOrder(string $orderReference): array
    {
        $token = $this->getAccessToken();

        $url = sprintf('%s/transactions/outlets/%s/orders/%s', $this->baseUrl, $this->outletId, $orderReference);

        if (config('ngenius.log_requests', true)) {
            Log::info('N-Genius: Retrieving order', [
                'reference' => $orderReference,
                'url' => $url,
            ]);
        }

        $res = Http::timeout(config('ngenius.timeout', 30))
            ->withToken($token)
            ->get($url);

        if (!$res->successful()) {
            Log::error('N-Genius retrieve order failed', [
                'status' => $res->status(),
                'body' => $res->body(),
                'orderReference' => $orderReference,
            ]);
            throw new RuntimeException('Failed to retrieve N-Genius order status: ' . $res->status());
        }

        $response = (array) $res->json();

        if (config('ngenius.log_responses', true)) {
            Log::info('N-Genius: Order retrieved successfully', [
                'reference' => $orderReference,
                'state' => Arr::get($response, 'state'),
            ]);
        }

        return $response;
    }

    /**
     * Capture an authorized payment.
     *
     * @param string $orderReference The order reference
     * @param int $amountMinor Amount to capture in minor units
     * @return array Capture response
     */
    public function capturePayment(string $orderReference, int $amountMinor): array
    {
        $token = $this->getAccessToken();

        $url = sprintf(
            '%s/transactions/outlets/%s/orders/%s/payments/capture',
            $this->baseUrl,
            $this->outletId,
            $orderReference
        );

        $payload = [
            'amount' => [
                'currencyCode' => config('ngenius.currency', 'AED'),
                'value' => $amountMinor,
            ],
        ];

        Log::info('N-Genius: Capturing payment', [
            'reference' => $orderReference,
            'amount' => $amountMinor,
        ]);

        $res = Http::timeout(config('ngenius.timeout', 30))
            ->accept('application/vnd.ni-payment.v2+json')
            ->withToken($token)
            ->withHeaders([
                'content-type' => 'application/vnd.ni-payment.v2+json',
            ])
            ->post($url, $payload);

        if (!$res->successful()) {
            Log::error('N-Genius capture payment failed', [
                'status' => $res->status(),
                'body' => $res->body(),
                'orderReference' => $orderReference,
            ]);
            throw new RuntimeException('Failed to capture N-Genius payment: ' . $res->status());
        }

        $response = (array) $res->json();

        Log::info('N-Genius: Payment captured successfully', [
            'reference' => $orderReference,
        ]);

        return $response;
    }

    /**
     * Void/Cancel an authorized payment.
     *
     * @param string $orderReference The order reference
     * @return array Void response
     */
    public function voidPayment(string $orderReference): array
    {
        $token = $this->getAccessToken();

        $url = sprintf(
            '%s/transactions/outlets/%s/orders/%s/payments/void',
            $this->baseUrl,
            $this->outletId,
            $orderReference
        );

        Log::info('N-Genius: Voiding payment', [
            'reference' => $orderReference,
        ]);

        $res = Http::timeout(config('ngenius.timeout', 30))
            ->accept('application/vnd.ni-payment.v2+json')
            ->withToken($token)
            ->withHeaders([
                'content-type' => 'application/vnd.ni-payment.v2+json',
            ])
            ->post($url, []);

        if (!$res->successful()) {
            Log::error('N-Genius void payment failed', [
                'status' => $res->status(),
                'body' => $res->body(),
                'orderReference' => $orderReference,
            ]);
            throw new RuntimeException('Failed to void N-Genius payment: ' . $res->status());
        }

        $response = (array) $res->json();

        Log::info('N-Genius: Payment voided successfully', [
            'reference' => $orderReference,
        ]);

        return $response;
    }

    /**
     * Refund a captured payment.
     *
     * @param string $orderReference The order reference
     * @param int $amountMinor Amount to refund in minor units
     * @return array Refund response
     */
    public function refundPayment(string $orderReference, int $amountMinor): array
    {
        $token = $this->getAccessToken();

        $url = sprintf(
            '%s/transactions/outlets/%s/orders/%s/payments/refund',
            $this->baseUrl,
            $this->outletId,
            $orderReference
        );

        $payload = [
            'amount' => [
                'currencyCode' => config('ngenius.currency', 'AED'),
                'value' => $amountMinor,
            ],
        ];

        Log::info('N-Genius: Refunding payment', [
            'reference' => $orderReference,
            'amount' => $amountMinor,
        ]);

        $res = Http::timeout(config('ngenius.timeout', 30))
            ->accept('application/vnd.ni-payment.v2+json')
            ->withToken($token)
            ->withHeaders([
                'content-type' => 'application/vnd.ni-payment.v2+json',
            ])
            ->post($url, $payload);

        if (!$res->successful()) {
            Log::error('N-Genius refund payment failed', [
                'status' => $res->status(),
                'body' => $res->body(),
                'orderReference' => $orderReference,
            ]);
            throw new RuntimeException('Failed to refund N-Genius payment: ' . $res->status());
        }

        $response = (array) $res->json();

        Log::info('N-Genius: Payment refunded successfully', [
            'reference' => $orderReference,
        ]);

        return $response;
    }

    /**
     * Create a payment order using a saved card token.
     *
     * @param string $cardToken The saved card token
     * @param int $amountMinor Amount in minor units
     * @param string $currencyCode Currency code
     * @param string $cvv Card CVV
     * @return array Payment response
     */
    public function payWithToken(
        string $cardToken,
        int $amountMinor,
        string $currencyCode,
        string $cvv
    ): array {
        $token = $this->getAccessToken();

        $url = sprintf('%s/transactions/outlets/%s/orders', $this->baseUrl, $this->outletId);

        $payload = [
            'action' => config('ngenius.default_action', 'PURCHASE'),
            'amount' => [
                'currencyCode' => $currencyCode,
                'value' => $amountMinor,
            ],
            'paymentMethod' => [
                'token' => $cardToken,
                'cvv' => $cvv,
            ],
        ];

        Log::info('N-Genius: Creating token payment', [
            'token' => substr($cardToken, 0, 10) . '...',
            'amount' => $amountMinor,
        ]);

        $res = Http::timeout(config('ngenius.timeout', 30))
            ->accept('application/vnd.ni-payment.v2+json')
            ->withToken($token)
            ->withHeaders([
                'content-type' => 'application/vnd.ni-payment.v2+json',
            ])
            ->post($url, $payload);

        if (!$res->successful()) {
            Log::error('N-Genius token payment failed', [
                'status' => $res->status(),
                'body' => $res->body(),
            ]);
            throw new RuntimeException('Failed to process N-Genius token payment: ' . $res->status());
        }

        $response = (array) $res->json();

        Log::info('N-Genius: Token payment processed successfully');

        return $response;
    }

    /**
     * Parse payment state from N-Genius order response.
     *
     * @param array $orderData The order data from N-Genius
     * @return array{state: string|null, transaction_id: string|null, card_info: array|null, error_message: string|null, payment_state: string|null}
     */
    public function parsePaymentState(array $orderData): array
    {
        $paymentState = null;
        $transactionId = null;
        $cardInfo = null;
        $errorMessage = null;
        $rawPaymentState = null;

        // Try to get payment state from embedded payments
        $payments = Arr::get($orderData, '_embedded.payment', []);
        if (is_array($payments) && !empty($payments[0]) && is_array($payments[0])) {
            $payment = $payments[0];
            $rawPaymentState = Arr::get($payment, 'state');
            $paymentState = $rawPaymentState;
            $transactionId = Arr::get($payment, 'reference');

            // Extract card information
            $cardInfo = [
                'scheme' => Arr::get($payment, 'paymentMethod.cardScheme'),
                'last4' => Arr::get($payment, 'paymentMethod.pan'),
                'expiry' => Arr::get($payment, 'paymentMethod.expiry'),
                'cardholder' => Arr::get($payment, 'paymentMethod.cardholderName'),
            ];

            // Extract error message if failed
            $errorMessage = Arr::get($payment, 'failureReason') ?? Arr::get($payment, 'authResponse.message');
        }

        // Fallback to top-level state
        if (!$paymentState) {
            $paymentState = Arr::get($orderData, 'state');
            $rawPaymentState = $paymentState;
        }

        return [
            'state' => is_string($paymentState) ? strtoupper($paymentState) : null,
            'transaction_id' => $transactionId,
            'card_info' => $cardInfo,
            'error_message' => $errorMessage,
            'payment_state' => $rawPaymentState,
        ];
    }
}
