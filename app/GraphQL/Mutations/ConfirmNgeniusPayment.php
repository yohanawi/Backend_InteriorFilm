<?php

namespace App\GraphQL\Mutations;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Services\NgeniusClient;
use GraphQL\Error\Error;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ConfirmNgeniusPayment
{
    public function __invoke($rootValue, array $args, $context, $resolveInfo)
    {
        $customer = Auth::guard('sanctum')->user();

        $orderNumber = (string)($args['order_number'] ?? '');
        $ref = (string)($args['ref'] ?? '');

        if (!$orderNumber || !$ref) {
            throw new Error('Missing order_number or ref');
        }

        /** @var Order $order */
        $order = Order::query()
            ->where('order_number', $orderNumber)
            ->where('customer_id', $customer->id)
            ->first();

        if (!$order) {
            throw new Error('Order not found');
        }

        if ($order->payment_method !== 'ngenius_hpp') {
            throw new Error('Order is not an N-Genius payment');
        }

        $ngenius = NgeniusClient::fromConfig();
        $raw = $ngenius->retrieveOrder($ref);

        // Use enhanced payment state parser
        $parsed = $ngenius->parsePaymentState($raw);
        $paymentState = $parsed['state'];
        $transactionId = $parsed['transaction_id'];
        $cardInfo = $parsed['card_info'];

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

        DB::beginTransaction();

        try {
            $order->ngenius_reference = $ref;
            $order->ngenius_last_payment_state = $paymentState;

            // Save transaction ID if available
            if ($transactionId) {
                $order->transaction_id = $transactionId;
            }

            // Save card information if available
            if (!empty($cardInfo['last4'])) {
                $order->card_last4 = $cardInfo['last4'];
                $order->card_scheme = $cardInfo['scheme'];
            }

            if ($paymentState && in_array($paymentState, $paidStates, true)) {
                $order->payment_status = 'paid';
                if ($order->status === 'pending') {
                    $order->status = 'processing';
                }
                if (!$order->paid_at) {
                    $order->paid_at = now();
                }

                $comment = 'Payment successful via N-Genius';
                if ($paymentState === 'AUTHORISED' || $paymentState === 'AUTHORIZED') {
                    $comment .= ' (Authorized - will be captured)';
                }
                if ($paymentState) {
                    $comment .= " ({$paymentState})";
                }
                if (!empty($cardInfo['scheme']) && !empty($cardInfo['last4'])) {
                    $comment .= " - {$cardInfo['scheme']} ending in {$cardInfo['last4']}";
                }

                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'status' => $order->status,
                    'comment' => $comment,
                    'notify_customer' => true,
                ]);
            } elseif ($paymentState && in_array($paymentState, $failedStates, true)) {
                $order->payment_status = 'failed';

                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'status' => $order->status,
                    'comment' => 'Payment failed via N-Genius' . ($paymentState ? " ({$paymentState})" : ''),
                    'notify_customer' => true,
                ]);
            } else {
                // Still pending / in progress (e.g. STARTED, AWAIT_3DS)
                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'status' => $order->status,
                    'comment' => 'Payment status checked via N-Genius' . ($paymentState ? " ({$paymentState})" : ''),
                    'notify_customer' => false,
                ]);
            }

            $order->save();

            DB::commit();

            $order->load(['items.product', 'customer']);

            return [
                'order' => $order,
                'message' => 'Payment status updated',
                'success' => true,
                'payment_url' => $order->ngenius_payment_url,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw new Error('Failed to confirm payment: ' . $e->getMessage());
        }
    }
}
