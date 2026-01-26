<?php

namespace App\GraphQL\Mutations;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Services\NgeniusClient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use GraphQL\Error\Error;

class CreateOrder
{
    public function __invoke($rootValue, array $args, $context, $resolveInfo)
    {
        $customer = Auth::guard('sanctum')->user();

        DB::beginTransaction();

        try {
            // Validate and calculate order totals
            $subtotal = 0;
            $itemsData = [];

            foreach ($args['items'] as $item) {
                $product = Product::find($item['product_id']);

                if (!$product) {
                    throw new Error("Product with ID {$item['product_id']} not found");
                }

                if (!$product->is_active) {
                    throw new Error("Product '{$product->name}' is not available");
                }

                // Check stock availability
                if ($product->stock_warehouse < $item['quantity'] && !$product->allow_backorders) {
                    throw new Error("Insufficient stock for product '{$product->name}'");
                }

                // Calculate price with discount
                $unitPrice = $product->price;
                if ($product->discount_value > 0) {
                    if ($product->discount_type === 'percentage' || $product->discount_type === 'percent') {
                        $unitPrice = $product->price - ($product->price * $product->discount_value / 100);
                    } elseif ($product->discount_type === 'fixed' || $product->discount_type === 'amount') {
                        $unitPrice = $product->price - $product->discount_value;
                    }
                }
                $unitPrice = max(0, round($unitPrice, 2));

                $itemSubtotal = $unitPrice * $item['quantity'];
                $itemTax = round($itemSubtotal * 0.08, 2); // 8% tax
                $itemTotal = round($itemSubtotal + $itemTax, 2);

                $subtotal += $itemSubtotal;

                $itemsData[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'unit_price' => $unitPrice,
                    'subtotal' => $itemSubtotal,
                    'tax' => $itemTax,
                    'total' => $itemTotal,
                ];
            }

            // Calculate order totals
            $tax = round($subtotal * 0.08, 2); // 8% tax
            $shippingCost = 0; // Shipping is free
            $discount = 0; // TODO: Implement coupon logic
            $total = round($subtotal + $tax + $shippingCost - $discount, 2);

            // Prepare billing address
            $billingData = [];
            if ($args['billing_same_as_shipping']) {
                $billingData = [
                    'billing_first_name' => $args['shipping_first_name'],
                    'billing_last_name' => $args['shipping_last_name'],
                    'billing_email' => $args['shipping_email'],
                    'billing_phone' => $args['shipping_phone'],
                    'billing_address' => $args['shipping_address'],
                    'billing_city' => $args['shipping_city'],
                    'billing_state' => $args['shipping_state'] ?? null,
                    'billing_country' => $args['shipping_country'],
                    'billing_postal_code' => $args['shipping_postal_code'],
                ];
            } else {
                $billingData = [
                    'billing_first_name' => $args['billing_first_name'] ?? null,
                    'billing_last_name' => $args['billing_last_name'] ?? null,
                    'billing_email' => $args['billing_email'] ?? null,
                    'billing_phone' => $args['billing_phone'] ?? null,
                    'billing_address' => $args['billing_address'] ?? null,
                    'billing_city' => $args['billing_city'] ?? null,
                    'billing_state' => $args['billing_state'] ?? null,
                    'billing_country' => $args['billing_country'] ?? null,
                    'billing_postal_code' => $args['billing_postal_code'] ?? null,
                ];
            }

            // Create order
            $order = Order::create([
                'customer_id' => $customer->id,
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => $args['payment_method'],
                'shipping_first_name' => $args['shipping_first_name'],
                'shipping_last_name' => $args['shipping_last_name'],
                'shipping_email' => $args['shipping_email'],
                'shipping_phone' => $args['shipping_phone'],
                'shipping_address' => $args['shipping_address'],
                'shipping_city' => $args['shipping_city'],
                'shipping_state' => $args['shipping_state'] ?? null,
                'shipping_country' => $args['shipping_country'],
                'shipping_postal_code' => $args['shipping_postal_code'],
                ...$billingData,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping_cost' => $shippingCost,
                'discount' => $discount,
                'total' => $total,
                'notes' => $args['notes'] ?? null,
                'coupon_code' => $args['coupon_code'] ?? null,
            ]);

            // Create order items and update stock
            foreach ($itemsData as $itemData) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $itemData['product']->id,
                    'product_name' => $itemData['product']->name,
                    'product_slug' => $itemData['product']->slug,
                    'product_sku' => $itemData['product']->sku,
                    'product_image' => $itemData['product']->thumbnail ?? ($itemData['product']->images[0] ?? null),
                    'unit_price' => $itemData['unit_price'],
                    'quantity' => $itemData['quantity'],
                    'subtotal' => $itemData['subtotal'],
                    'tax' => $itemData['tax'],
                    'total' => $itemData['total'],
                ]);

                // Update stock from warehouse
                $product = $itemData['product'];
                $product->stock_warehouse -= $itemData['quantity'];
                $product->save();
            }

            // Create initial status history
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => 'pending',
                'comment' => 'Order created',
                'notify_customer' => false,
            ]);

            $paymentUrl = null;

            if (($args['payment_method'] ?? null) === 'ngenius_hpp') {
                // Get redirect URL from new config or fallback to old
                $redirectBase = (string) (
                    config('ngenius.redirect_url') ??
                    config('services.ngenius.redirect_url_base') ??
                    ''
                );

                if (!$redirectBase) {
                    throw new Error('N-Genius redirect URL is not configured. Set NGENIUS_REDIRECT_URL in .env');
                }

                $redirectUrl = $redirectBase . (str_contains($redirectBase, '?') ? '&' : '?') . 'order_number=' . urlencode($order->order_number);

                // Get currency from new config or fallback
                $currency = (string) (
                    config('ngenius.currency') ??
                    config('services.ngenius.currency') ??
                    'AED'
                );

                $amountMinor = (int) round($total * 100);

                try {
                    $ngenius = NgeniusClient::fromConfig();

                    // Prepare additional options for enhanced features
                    $additionalOptions = [
                        'merchant_order_reference' => $order->order_number,
                        'customer_name' => $order->shipping_first_name . ' ' . $order->shipping_last_name,
                    ];

                    // Add billing address if available
                    if ($order->billing_address_line1) {
                        $additionalOptions['billing_address'] = [
                            'firstName' => $order->billing_first_name,
                            'lastName' => $order->billing_last_name,
                            'address1' => $order->billing_address_line1,
                            'address2' => $order->billing_address_line2,
                            'city' => $order->billing_city,
                            'stateProvince' => $order->billing_state,
                            'countryCode' => $order->billing_country,
                            'postalCode' => $order->billing_zip_code,
                        ];
                    }

                    // Add shipping address
                    $additionalOptions['shipping_address'] = [
                        'firstName' => $order->shipping_first_name,
                        'lastName' => $order->shipping_last_name,
                        'address1' => $order->shipping_address_line1,
                        'address2' => $order->shipping_address_line2,
                        'city' => $order->shipping_city,
                        'stateProvince' => $order->shipping_state,
                        'countryCode' => $order->shipping_country,
                        'postalCode' => $order->shipping_zip_code,
                    ];

                    $ngOrder = $ngenius->createHostedPaymentOrder(
                        amountMinor: $amountMinor,
                        currencyCode: $currency,
                        redirectUrl: $redirectUrl,
                        emailAddress: $order->shipping_email,
                        action: config('ngenius.default_action', 'PURCHASE'),
                        additionalOptions: $additionalOptions
                    );

                    $order->ngenius_reference = $ngOrder['reference'];
                    $order->ngenius_payment_url = $ngOrder['payment_url'];
                    $order->ngenius_currency = $currency;
                    $order->ngenius_amount_minor = $amountMinor;
                    $order->save();

                    OrderStatusHistory::create([
                        'order_id' => $order->id,
                        'status' => 'pending',
                        'comment' => 'Payment initiated via N-Genius (Ref: ' . $ngOrder['reference'] . ')',
                        'notify_customer' => false,
                    ]);

                    $paymentUrl = $ngOrder['payment_url'];
                } catch (\RuntimeException $e) {
                    // If N-Genius order creation fails, rollback and notify user
                    DB::rollBack();

                    Log::error('N-Genius order creation failed during checkout', [
                        'order_number' => $order->order_number,
                        'error' => $e->getMessage(),
                    ]);

                    throw new Error('Payment gateway error: Unable to initialize payment. Please try again or use a different payment method.');
                }
            }

            DB::commit();

            // Load relationships
            $order->load(['items.product', 'customer']);

            return [
                'order' => $order,
                'message' => 'Order created successfully',
                'success' => true,
                'payment_url' => $paymentUrl,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw new Error('Failed to create order: ' . $e->getMessage());
        }
    }
}
