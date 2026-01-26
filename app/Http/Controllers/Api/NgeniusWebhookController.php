<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Services\NgeniusClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NgeniusWebhookController extends Controller
{
    /**
     * Handle N-Genius webhook notifications.
     *
     * N-Genius sends webhooks for various payment events:
     * - STARTED: Payment initiated
     * - AWAIT_3DS: Waiting for 3D Secure authentication
     * - AUTHORISED: Payment authorized (requires capture)
     * - PURCHASED/CAPTURED: Payment captured successfully
     * - FAILED: Payment failed
     * - DECLINED: Payment declined
     * - CANCELLED: Payment cancelled
     * - REVERSED: Payment reversed
     */
    public function handle(Request $request)
    {
        try {
            Log::info('N-Genius Webhook Received', [
                'headers' => $request->headers->all(),
                'body' => $request->all(),
            ]);

            // Verify webhook signature if configured
            if ($secret = config('ngenius.webhook_secret')) {
                if (!$this->verifySignature($request, $secret)) {
                    Log::warning('N-Genius Webhook: Invalid signature');
                    return response()->json(['error' => 'Invalid signature'], 401);
                }
            }

            $payload = $request->all();

            // Extract order reference
            $orderReference = $payload['orderReference'] ?? $payload['ref'] ?? null;

            if (!$orderReference) {
                Log::warning('N-Genius Webhook: Missing order reference');
                return response()->json(['error' => 'Missing order reference'], 400);
            }

            // Find the order
            $order = Order::where('ngenius_reference', $orderReference)->first();

            if (!$order) {
                Log::warning('N-Genius Webhook: Order not found', [
                    'reference' => $orderReference,
                ]);
                return response()->json(['error' => 'Order not found'], 404);
            }

            // Get full order details from N-Genius
            $ngenius = NgeniusClient::fromConfig();
            $orderData = $ngenius->retrieveOrder($orderReference);

            // Parse payment state
            $parsed = $ngenius->parsePaymentState($orderData);
            $paymentState = $parsed['state'];

            Log::info('N-Genius Webhook: Processing payment state', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'state' => $paymentState,
            ]);

            // Update order based on payment state
            $this->updateOrderStatus($order, $paymentState, $parsed);

            return response()->json([
                'success' => true,
                'message' => 'Webhook processed successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('N-Genius Webhook Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Webhook processing failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verify webhook signature.
     */
    private function verifySignature(Request $request, string $secret): bool
    {
        $signature = $request->header('X-Ngenius-Signature');

        if (!$signature) {
            return false;
        }

        $payload = $request->getContent();
        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Update order status based on payment state.
     */
    private function updateOrderStatus(Order $order, ?string $paymentState, array $parsedData): void
    {
        DB::beginTransaction();

        try {
            // Update payment state
            $order->ngenius_last_payment_state = $paymentState;

            // Save card information if available
            if (!empty($parsedData['card_info']['last4'])) {
                $order->card_last4 = $parsedData['card_info']['last4'];
                $order->card_scheme = $parsedData['card_info']['scheme'];
            }

            $paidStates = [
                'CAPTURED',
                'PURCHASED',
                'AUTHORISED',
                'AUTHORIZED',
                'SUCCESS',
            ];

            $failedStates = [
                'FAILED',
                'DECLINED',
                'CANCELLED',
                'CANCELED',
                'REVERSED',
            ];

            if ($paymentState && in_array($paymentState, $paidStates, true)) {
                // Payment successful
                if ($order->payment_status !== 'paid') {
                    $order->payment_status = 'paid';

                    if ($order->status === 'pending') {
                        $order->status = 'processing';
                    }

                    if (!$order->paid_at) {
                        $order->paid_at = now();
                    }

                    OrderStatusHistory::create([
                        'order_id' => $order->id,
                        'status' => $order->status,
                        'comment' => sprintf(
                            'Payment %s via N-Genius (%s)',
                            $paymentState === 'AUTHORISED' ? 'authorized' : 'captured',
                            $paymentState
                        ),
                        'notify_customer' => true,
                    ]);

                    Log::info('N-Genius: Order payment successful', [
                        'order_id' => $order->id,
                        'state' => $paymentState,
                    ]);
                }
            } elseif ($paymentState && in_array($paymentState, $failedStates, true)) {
                // Payment failed
                if ($order->payment_status !== 'failed') {
                    $order->payment_status = 'failed';

                    OrderStatusHistory::create([
                        'order_id' => $order->id,
                        'status' => $order->status,
                        'comment' => sprintf('Payment failed via N-Genius (%s)', $paymentState),
                        'notify_customer' => true,
                    ]);

                    Log::info('N-Genius: Order payment failed', [
                        'order_id' => $order->id,
                        'state' => $paymentState,
                    ]);
                }
            } else {
                // Payment in progress (STARTED, AWAIT_3DS, etc.)
                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'status' => $order->status,
                    'comment' => sprintf('Payment status: %s', $paymentState ?? 'Unknown'),
                    'notify_customer' => false,
                ]);

                Log::info('N-Genius: Order payment in progress', [
                    'order_id' => $order->id,
                    'state' => $paymentState,
                ]);
            }

            $order->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
