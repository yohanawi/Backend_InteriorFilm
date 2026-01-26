<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Services\NgeniusClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Handle customer return from N-Genius Hosted Payment Page
 */
class PaymentReturnController extends Controller
{
    /**
     * Handle N-Genius payment return
     * 
     * Customer is redirected here after completing payment on N-Genius HPP
     */
    public function ngeniusReturn(Request $request)
    {
        try {
            // Get order reference from query parameters
            $orderRef = $request->query('ref');
            $orderNumber = $request->query('order_number');

            Log::info('N-Genius payment return', [
                'ref' => $orderRef,
                'order_number' => $orderNumber,
            ]);

            // Find order
            $order = null;
            if ($orderRef) {
                $order = Order::where('ngenius_reference', $orderRef)->first();
            } elseif ($orderNumber) {
                $order = Order::where('order_number', $orderNumber)->first();
                $orderRef = $order->ngenius_reference ?? null;
            }

            if (!$order) {
                Log::error('Order not found on payment return', [
                    'ref' => $orderRef,
                    'order_number' => $orderNumber,
                ]);

                return $this->redirectToFrontend('/order/payment-failed', [
                    'reason' => 'order_not_found',
                    'message' => 'Order not found',
                ]);
            }

            // Retrieve payment status from N-Genius
            try {
                $ngenius = NgeniusClient::fromConfig();
                $orderData = $ngenius->retrieveOrder($orderRef);
                $parsed = $ngenius->parsePaymentState($orderData);

                Log::info('N-Genius payment status', [
                    'order_number' => $order->order_number,
                    'status' => $parsed['status'],
                    'raw_state' => $parsed['raw_state'],
                ]);

                DB::beginTransaction();

                // Handle based on payment status
                switch ($parsed['status']) {
                    case 'SUCCESS':
                        $this->handleSuccessfulPayment($order, $parsed);
                        $redirectPath = '/order/payment-success';
                        $redirectParams = ['order_number' => $order->order_number];
                        break;

                    case 'FAILED':
                        $this->handleFailedPayment($order, $parsed);
                        $redirectPath = '/order/payment-failed';
                        $redirectParams = [
                            'order_number' => $order->order_number,
                            'reason' => 'payment_failed',
                            'message' => $parsed['message'],
                        ];
                        break;

                    case 'CANCELLED':
                        $this->handleCancelledPayment($order, $parsed);
                        $redirectPath = '/order/payment-cancelled';
                        $redirectParams = ['order_number' => $order->order_number];
                        break;

                    default:
                        $redirectPath = '/order/payment-failed';
                        $redirectParams = [
                            'order_number' => $order->order_number,
                            'reason' => 'unknown_status',
                            'message' => 'Payment status unknown',
                        ];
                }

                DB::commit();

                return $this->redirectToFrontend($redirectPath, $redirectParams);
            } catch (\Exception $e) {
                DB::rollBack();

                Log::error('Failed to retrieve N-Genius payment status', [
                    'order_number' => $order->order_number,
                    'error' => $e->getMessage(),
                ]);

                return $this->redirectToFrontend('/order/payment-failed', [
                    'order_number' => $order->order_number,
                    'reason' => 'verification_failed',
                    'message' => 'Could not verify payment status',
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Payment return error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->redirectToFrontend('/order/payment-failed', [
                'reason' => 'system_error',
                'message' => 'An error occurred',
            ]);
        }
    }

    /**
     * Handle successful payment
     */
    private function handleSuccessfulPayment(Order $order, array $parsed): void
    {
        // Update order payment status
        $order->payment_status = 'completed';
        $order->payment_transaction_id = $parsed['transaction_id'];
        $order->payment_completed_at = now();
        $order->status = 'processing';
        $order->save();

        // Reduce product stock
        foreach ($order->items as $item) {
            if ($item->product && $item->product->stock_warehouse > 0) {
                $item->product->decrement('stock_warehouse', $item->quantity);
            }
        }

        // Add status history
        OrderStatusHistory::create([
            'order_id' => $order->id,
            'status' => 'processing',
            'comment' => 'Payment completed successfully via N-Genius (Transaction: ' . $parsed['transaction_id'] . ')',
            'notify_customer' => true,
        ]);

        Log::info('Payment successful', [
            'order_number' => $order->order_number,
            'transaction_id' => $parsed['transaction_id'],
        ]);
    }

    /**
     * Handle failed payment
     */
    private function handleFailedPayment(Order $order, array $parsed): void
    {
        // Update order payment status
        $order->payment_status = 'failed';
        $order->payment_error_message = $parsed['message'];
        $order->status = 'cancelled';
        $order->save();

        // Add status history
        OrderStatusHistory::create([
            'order_id' => $order->id,
            'status' => 'cancelled',
            'comment' => 'Payment failed: ' . $parsed['message'],
            'notify_customer' => false,
        ]);

        Log::warning('Payment failed', [
            'order_number' => $order->order_number,
            'message' => $parsed['message'],
        ]);
    }

    /**
     * Handle cancelled payment
     */
    private function handleCancelledPayment(Order $order, array $parsed): void
    {
        // Update order payment status
        $order->payment_status = 'cancelled';
        $order->payment_error_message = $parsed['message'];
        $order->status = 'cancelled';
        $order->save();

        // Add status history
        OrderStatusHistory::create([
            'order_id' => $order->id,
            'status' => 'cancelled',
            'comment' => 'Payment cancelled by customer',
            'notify_customer' => false,
        ]);

        Log::info('Payment cancelled', [
            'order_number' => $order->order_number,
        ]);
    }

    /**
     * Redirect to frontend with parameters
     */
    private function redirectToFrontend(string $path, array $params = [])
    {
        $frontendUrl = config('app.frontend_url', 'http://localhost:3000');
        $url = $frontendUrl . $path;

        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        return redirect($url);
    }
}
