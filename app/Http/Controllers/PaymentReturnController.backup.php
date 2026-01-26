<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Services\NgeniusClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentReturnController extends Controller
{
    /**
     * Handle N-Genius payment return (success, failed, or cancelled).
     *
     * This endpoint is called when the customer returns from the N-Genius payment page.
     * It checks the payment status and redirects to the appropriate Next.js page.
     */
    public function ngeniusReturn(Request $request)
    {
        try {
            // Extract order reference from query parameters
            $orderRef = $request->query('ref');
            $orderNumber = $request->query('order_number');

            Log::info('N-Genius payment return', [
                'ref' => $orderRef,
                'order_number' => $orderNumber,
                'all_params' => $request->all(),
            ]);

            // Find order by reference or order number
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

                return redirect(config('app.frontend_url', 'http://localhost:3000') . '/order/payment-failed?reason=order_not_found');
            }

            // Check payment status with N-Genius
            try {
                $ngenius = NgeniusClient::fromConfig();
                $orderData = $ngenius->retrieveOrder($orderRef);
                $parsed = $ngenius->parsePaymentState($orderData);

                Log::info('N-Genius payment status retrieved', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'state' => $parsed['state'],
                    'payment_state' => $parsed['payment_state'] ?? null,
                ]);

                // Update order based on payment state
                DB::beginTransaction();

                $paymentState = $parsed['state'];
                $paymentStatus = 'pending';
                $orderStatus = 'pending';
                $redirectUrl = '';

                switch ($paymentState) {
                    case 'CAPTURED':
                    case 'PURCHASED':
                        $paymentStatus = 'completed';
                        $orderStatus = 'processing';
                        $redirectUrl = '/order/payment-success';

                        $order->payment_completed_at = now();
                        $order->payment_transaction_id = $parsed['transaction_id'] ?? $orderRef;

                        // Update stock
                        foreach ($order->items as $item) {
                            if ($item->product) {
                                $item->product->decrement('stock_warehouse', $item->quantity);
                            }
                        }

                        OrderStatusHistory::create([
                            'order_id' => $order->id,
                            'status' => $orderStatus,
                            'comment' => 'Payment completed successfully via N-Genius',
                            'notify_customer' => true,
                        ]);
                        break;

                    case 'AUTHORISED':
                        $paymentStatus = 'processing';
                        $orderStatus = 'pending';
                        $redirectUrl = '/order/payment-success';

                        $order->payment_transaction_id = $parsed['transaction_id'] ?? $orderRef;

                        OrderStatusHistory::create([
                            'order_id' => $order->id,
                            'status' => $orderStatus,
                            'comment' => 'Payment authorized via N-Genius (requires capture)',
                            'notify_customer' => false,
                        ]);
                        break;

                    case 'FAILED':
                    case 'DECLINED':
                        $paymentStatus = 'failed';
                        $orderStatus = 'cancelled';
                        $redirectUrl = '/order/payment-failed';

                        $order->payment_error_message = $parsed['error_message'] ?? 'Payment was declined';

                        OrderStatusHistory::create([
                            'order_id' => $order->id,
                            'status' => $orderStatus,
                            'comment' => 'Payment failed: ' . ($parsed['error_message'] ?? 'Unknown error'),
                            'notify_customer' => true,
                        ]);
                        break;

                    case 'CANCELLED':
                        $paymentStatus = 'cancelled';
                        $orderStatus = 'cancelled';
                        $redirectUrl = '/order/payment-cancelled';

                        OrderStatusHistory::create([
                            'order_id' => $order->id,
                            'status' => $orderStatus,
                            'comment' => 'Payment cancelled by customer',
                            'notify_customer' => false,
                        ]);
                        break;

                    case 'AWAIT_3DS':
                    case 'STARTED':
                        $paymentStatus = 'processing';
                        $redirectUrl = '/order/payment-processing';
                        break;

                    default:
                        $paymentStatus = 'pending';
                        $redirectUrl = '/order/payment-pending';
                        Log::warning('Unknown payment state', [
                            'state' => $paymentState,
                            'order_id' => $order->id,
                        ]);
                }

                $order->payment_status = $paymentStatus;
                $order->status = $orderStatus;
                $order->payment_metadata = json_encode([
                    'ngenius_state' => $paymentState,
                    'payment_state' => $parsed['payment_state'] ?? null,
                    'last_updated' => now()->toIso8601String(),
                    'raw_data' => $parsed,
                ]);
                $order->save();

                DB::commit();

                // Redirect to Next.js with order information
                $frontendUrl = config('app.frontend_url', 'http://localhost:3000');
                $redirectUrl = $frontendUrl . $redirectUrl . '?order_number=' . urlencode($order->order_number);

                return redirect($redirectUrl);
            } catch (\Exception $e) {
                DB::rollBack();

                Log::error('Failed to retrieve N-Genius payment status', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                // Redirect to error page
                $frontendUrl = config('app.frontend_url', 'http://localhost:3000');
                return redirect($frontendUrl . '/order/payment-failed?reason=status_check_failed&order_number=' . urlencode($order->order_number));
            }
        } catch (\Exception $e) {
            Log::error('Payment return error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $frontendUrl = config('app.frontend_url', 'http://localhost:3000');
            return redirect($frontendUrl . '/order/payment-failed?reason=system_error');
        }
    }
}
