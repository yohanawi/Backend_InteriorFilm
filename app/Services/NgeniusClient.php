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
        $environment = (string) config('ngenius.environment', 'sandbox');

        $baseUrl = $environment === 'production'
            ? (string) config('ngenius.production_url')
            : (string) config('ngenius.sandbox_url');

        $credentials = (array) config("ngenius.{$environment}");
        $outletId = (string) ($credentials['outlet_id'] ?? config('ngenius.outlet_id') ?? '');
        $apiKey = (string) ($credentials['api_key'] ?? config('ngenius.api_key') ?? '');
        $realmName = (string) ($credentials['realm_name'] ?? config('ngenius.realm_name') ?? 'ni');

        $baseUrl = rtrim($baseUrl, '/');

        if (!$baseUrl || !$outletId || !$apiKey) {
            throw new RuntimeException(
                'N-Genius is not configured. Set NGENIUS_SANDBOX_OUTLET_ID / NGENIUS_SANDBOX_API_KEY (or production equivalents) in .env'
            );
        }

        return new self($baseUrl, $outletId, $apiKey, $realmName);
    }

    /**
     * N-Genius access token is short-lived.
     * Cache slightly less than actual expiry to be safe.
     */
    public function getAccessToken(): string
    {
        $cacheKey = sprintf('ngenius:token:%s:%s', $this->baseUrl, $this->outletId);
        $ttl = (int) config('ngenius.token_cache_ttl', 240);

        return Cache::remember($cacheKey, now()->addSeconds($ttl), function () {
            $url = $this->baseUrl . '/identity/auth/access-token';

            if (config('ngenius.log_requests', true)) {
                Log::info('N-Genius: Requesting access token', ['url' => $url]);
            }

            $res = Http::timeout((int) config('ngenius.timeout', 30))
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

                $errorCode = (string) ($res->json('errors.0.errorCode') ?? '');
                $domain = (string) ($res->json('errors.0.domain') ?? '');
                $message = (string) ($res->json('errors.0.message') ?? '');
                $localized = (string) ($res->json('errors.0.localizedMessage') ?? '');
                $details = $message ?: $localized;
                $suffix = trim(
                    ($errorCode ? " {$errorCode}" : '')
                        . ($domain ? " ({$domain})" : '')
                        . ($details ? " - {$details}" : '')
                );

                throw new RuntimeException('Failed to obtain N-Genius access token: ' . $res->status() . ($suffix ? " -{$suffix}" : ''));
            }

            $token = (string) ($res->json('access_token') ?? '');
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
     * Create an HPP (Hosted Payment Page) order.
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

        $payload = [
            'action' => $action,
            'amount' => [
                'currencyCode' => $currencyCode,
                'value' => $amountMinor,
            ],
            'merchantAttributes' => [
                'redirectUrl' => $redirectUrl,
                'cancelUrl' => (string) (config('ngenius.cancel_url') ?: $redirectUrl),
            ],
        ];

        if ($emailAddress) {
            $payload['emailAddress'] = $emailAddress;
        }

        if ($language = config('ngenius.language')) {
            $payload['language'] = $language;
        }

        if (!empty($additionalOptions['billing_address'])) {
            $payload['billingAddress'] = $additionalOptions['billing_address'];
        }

        if (!empty($additionalOptions['shipping_address'])) {
            $payload['shippingAddress'] = $additionalOptions['shipping_address'];
        }

        if (config('ngenius.features.prepopulate_cardholder_name') && !empty($additionalOptions['customer_name'])) {
            $payload['merchantDefinedData'] = [
                'cardholderName' => $additionalOptions['customer_name'],
            ];
        }

        if (!empty($additionalOptions['merchant_order_reference'])) {
            $payload['merchantOrderReference'] = $additionalOptions['merchant_order_reference'];
        }

        if ($allowedMethods = $this->getAllowedPaymentMethods()) {
            $payload['paymentMethods'] = $allowedMethods;
        }

        if (config('ngenius.features.3ds_enabled')) {
            $payload['3ds'] = [
                'required' => true,
            ];
        }

        if (config('ngenius.features.tokenization_enabled') && !empty($additionalOptions['save_card'])) {
            $payload['merchantAttributes']['saveCard'] = true;
        }

        if (config('ngenius.features.slim_mode')) {
            $payload['merchantAttributes']['slimMode'] = true;
        }

        if (config('ngenius.features.mask_card_details')) {
            $payload['merchantAttributes']['maskCardDetails'] = true;
        }

        if ($orderTtl = config('ngenius.order_ttl')) {
            $payload['orderExpiry'] = $orderTtl;
        }

        if (config('ngenius.log_requests', true)) {
            Log::info('N-Genius: Creating order', [
                'url' => $url,
                'amount' => $amountMinor,
                'currency' => $currencyCode,
            ]);
        }

        $res = Http::timeout((int) config('ngenius.timeout', 30))
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

            $errorCode = (string) ($res->json('errors.0.errorCode') ?? '');
            $domain = (string) ($res->json('errors.0.domain') ?? '');
            $message = (string) ($res->json('errors.0.message') ?? '');
            $localized = (string) ($res->json('errors.0.localizedMessage') ?? '');
            $details = $message ?: $localized;
            $suffix = trim(
                ($errorCode ? " {$errorCode}" : '')
                    . ($domain ? " ({$domain})" : '')
                    . ($details ? " - {$details}" : '')
            );

            throw new RuntimeException('Failed to create N-Genius order: ' . $res->status() . ($suffix ? " -{$suffix}" : ''));
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
     * Backwards-compatible wrapper used by guest checkout + test scripts.
     *
     * @return array{reference:string,payment_url:string,order_id:string|null,raw_response:array}
     */
    public function createOrder(
        int $amountMinor,
        string $currencyCode,
        string $redirectUrl,
        ?string $emailAddress = null,
        ?string $orderReference = null
    ): array {
        $ngOrder = $this->createHostedPaymentOrder(
            amountMinor: $amountMinor,
            currencyCode: $currencyCode,
            redirectUrl: $redirectUrl,
            emailAddress: $emailAddress,
            action: (string) config('ngenius.default_action', 'PURCHASE'),
            additionalOptions: [
                'merchant_order_reference' => $orderReference,
            ]
        );

        $orderId = Arr::get($ngOrder, 'raw._id');

        return [
            'reference' => $ngOrder['reference'],
            'payment_url' => $ngOrder['payment_url'],
            'order_id' => is_string($orderId) ? $orderId : null,
            'raw_response' => (array) ($ngOrder['raw'] ?? []),
        ];
    }

    private function getAllowedPaymentMethods(): ?array
    {
        $methods = [];

        $paymentMethods = (array) config('ngenius.payment_methods', []);

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

        $res = Http::timeout((int) config('ngenius.timeout', 30))
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
                'currencyCode' => (string) config('ngenius.currency', 'AED'),
                'value' => $amountMinor,
            ],
        ];

        Log::info('N-Genius: Capturing payment', [
            'reference' => $orderReference,
            'amount' => $amountMinor,
        ]);

        $res = Http::timeout((int) config('ngenius.timeout', 30))
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

        $res = Http::timeout((int) config('ngenius.timeout', 30))
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
                'currencyCode' => (string) config('ngenius.currency', 'AED'),
                'value' => $amountMinor,
            ],
        ];

        Log::info('N-Genius: Refunding payment', [
            'reference' => $orderReference,
            'amount' => $amountMinor,
        ]);

        $res = Http::timeout((int) config('ngenius.timeout', 30))
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
            'action' => (string) config('ngenius.default_action', 'PURCHASE'),
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

        $res = Http::timeout((int) config('ngenius.timeout', 30))
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
     * @return array{state: string|null, transaction_id: string|null, card_info: array|null, error_message: string|null, payment_state: string|null}
     */
    public function parsePaymentState(array $orderData): array
    {
        $paymentState = null;
        $transactionId = null;
        $cardInfo = null;
        $errorMessage = null;
        $rawPaymentState = null;

        $payments = Arr::get($orderData, '_embedded.payment', []);
        if (is_array($payments) && !empty($payments[0]) && is_array($payments[0])) {
            $payment = $payments[0];
            $rawPaymentState = Arr::get($payment, 'state');
            $paymentState = $rawPaymentState;
            $transactionId = Arr::get($payment, 'reference');

            $cardInfo = [
                'scheme' => Arr::get($payment, 'paymentMethod.cardScheme'),
                'last4' => Arr::get($payment, 'paymentMethod.pan'),
                'expiry' => Arr::get($payment, 'paymentMethod.expiry'),
                'cardholder' => Arr::get($payment, 'paymentMethod.cardholderName'),
            ];

            $errorMessage = Arr::get($payment, 'failureReason') ?? Arr::get($payment, 'authResponse.message');
        }

        if (!$paymentState) {
            $paymentState = Arr::get($orderData, 'state');
            $rawPaymentState = $paymentState;
        }

        return [
            'state' => is_string($paymentState) ? strtoupper($paymentState) : null,
            'transaction_id' => is_string($transactionId) ? $transactionId : null,
            'card_info' => is_array($cardInfo) ? $cardInfo : null,
            'error_message' => is_string($errorMessage) ? $errorMessage : null,
            'payment_state' => is_string($rawPaymentState) ? $rawPaymentState : null,
        ];
    }
}
