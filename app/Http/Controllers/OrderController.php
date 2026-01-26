<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->getOrdersDataTable($request);
        }

        $statusColors = $this->getStatusColors();
        $paymentStatusColors = $this->getPaymentStatusColors();

        return view('pages.apps.orders.index', compact('statusColors', 'paymentStatusColors'));
    }

    /**
     * Get DataTables data for orders
     */
    private function getOrdersDataTable(Request $request)
    {
        $query = Order::with(['customer:id,first_name,last_name,email'])
            ->select('orders.*')
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->has('payment_status') && $request->payment_status !== 'all') {
            $query->where('payment_status', $request->payment_status);
        }

        return DataTables::of($query)
            ->addColumn('customer_name', function ($order) {
                return $order->customer
                    ? $order->customer->first_name . ' ' . $order->customer->last_name
                    : $order->shipping_full_name;
            })
            ->addColumn('customer_email', function ($order) {
                return $order->customer ? $order->customer->email : $order->shipping_email;
            })
            ->addColumn('status_badge', function ($order) {
                $colors = $this->getStatusColors();
                $color = $colors[$order->status] ?? 'secondary';
                return '<span class="badge badge-light-' . $color . '">' . ucfirst($order->status) . '</span>';
            })
            ->addColumn('payment_status_badge', function ($order) {
                $colors = $this->getPaymentStatusColors();
                $color = $colors[$order->payment_status] ?? 'secondary';
                return '<span class="badge badge-light-' . $color . '">' . ucfirst($order->payment_status) . '</span>';
            })
            ->addColumn('formatted_total', function ($order) {
                return 'AED ' . number_format($order->total_amount ?? $order->total, 2);
            })
            ->addColumn('formatted_date', function ($order) {
                return $order->created_at->format('M d, Y');
            })
            ->addColumn('action', function ($order) {
                return '<a href="' . route('orders.show', $order) . '" class="btn btn-sm btn-light btn-active-light-primary">View</a>';
            })
            ->filterColumn('customer_name', function ($query, $keyword) {
                $query->whereHas('customer', function ($q) use ($keyword) {
                    $q->where('first_name', 'like', "%{$keyword}%")
                        ->orWhere('last_name', 'like', "%{$keyword}%");
                })
                    ->orWhere('shipping_first_name', 'like', "%{$keyword}%")
                    ->orWhere('shipping_last_name', 'like', "%{$keyword}%");
            })
            ->rawColumns(['status_badge', 'payment_status_badge', 'action'])
            ->make(true);
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        $order->load(['customer', 'items.product', 'statusHistories.user']);

        $statusColors = $this->getStatusColors();
        $paymentStatusColors = $this->getPaymentStatusColors();

        return view('pages.apps.orders.show', compact('order', 'statusColors', 'paymentStatusColors'));
    }

    /**
     * Store a newly created order (from customer checkout)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'shipping_first_name' => 'required|string|max:255',
            'shipping_last_name' => 'required|string|max:255',
            'shipping_email' => 'required|email|max:255',
            'shipping_phone' => 'required|string|max:255',
            'shipping_address' => 'required|string',
            'shipping_city' => 'required|string|max:255',
            'shipping_state' => 'nullable|string|max:255',
            'shipping_country' => 'required|string|max:255',
            'shipping_postal_code' => 'required|string|max:255',
            'billing_first_name' => 'nullable|string|max:255',
            'billing_last_name' => 'nullable|string|max:255',
            'billing_email' => 'nullable|email|max:255',
            'billing_phone' => 'nullable|string|max:255',
            'billing_address' => 'nullable|string',
            'billing_city' => 'nullable|string|max:255',
            'billing_state' => 'nullable|string|max:255',
            'billing_country' => 'nullable|string|max:255',
            'billing_postal_code' => 'nullable|string|max:255',
            'payment_method' => 'required|in:credit_card,debit_card,paypal,stripe,cash_on_delivery,bank_transfer',
            'shipping_cost' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'coupon_code' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Create order
            $order = Order::create([
                'customer_id' => $validated['customer_id'],
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => $validated['payment_method'],
                'shipping_first_name' => $validated['shipping_first_name'],
                'shipping_last_name' => $validated['shipping_last_name'],
                'shipping_email' => $validated['shipping_email'],
                'shipping_phone' => $validated['shipping_phone'],
                'shipping_address' => $validated['shipping_address'],
                'shipping_city' => $validated['shipping_city'],
                'shipping_state' => $validated['shipping_state'] ?? null,
                'shipping_country' => $validated['shipping_country'],
                'shipping_postal_code' => $validated['shipping_postal_code'],
                'billing_first_name' => $validated['billing_first_name'] ?? $validated['shipping_first_name'],
                'billing_last_name' => $validated['billing_last_name'] ?? $validated['shipping_last_name'],
                'billing_email' => $validated['billing_email'] ?? $validated['shipping_email'],
                'billing_phone' => $validated['billing_phone'] ?? $validated['shipping_phone'],
                'billing_address' => $validated['billing_address'] ?? $validated['shipping_address'],
                'billing_city' => $validated['billing_city'] ?? $validated['shipping_city'],
                'billing_state' => $validated['billing_state'] ?? $validated['shipping_state'] ?? null,
                'billing_country' => $validated['billing_country'] ?? $validated['shipping_country'],
                'billing_postal_code' => $validated['billing_postal_code'] ?? $validated['shipping_postal_code'],
                'shipping_cost' => $validated['shipping_cost'] ?? 0,
                'discount' => $validated['discount'] ?? 0,
                'coupon_code' => $validated['coupon_code'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'subtotal' => 0,
                'tax' => 0,
                'total' => 0,
                'total_amount' => 0,
            ]);

            // Create order items
            $subtotal = 0;
            $tax = 0;

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);

                $unitPrice = $product->price;
                $quantity = $item['quantity'];
                $itemSubtotal = $unitPrice * $quantity;
                $itemTax = $itemSubtotal * 0.05; // 5% tax - adjust as needed
                $itemTotal = $itemSubtotal + $itemTax;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_slug' => $product->slug,
                    'product_sku' => $product->sku ?? null,
                    'product_image' => $product->image ?? null,
                    'unit_price' => $unitPrice,
                    'quantity' => $quantity,
                    'subtotal' => $itemSubtotal,
                    'tax' => $itemTax,
                    'total' => $itemTotal,
                    'attributes' => $item['attributes'] ?? null,
                ]);

                $subtotal += $itemSubtotal;
                $tax += $itemTax;
            }

            // Update order totals
            $order->subtotal = $subtotal;
            $order->tax = $tax;
            $order->total = $subtotal + $tax + $order->shipping_cost - $order->discount;
            $order->total_amount = $order->total;
            $order->save();

            // Create initial status history
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'user_id' => null,
                'status' => 'pending',
                'comment' => 'Order created',
                'notify_customer' => true,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'order' => $order->load('items'),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create order: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the order status.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,confirmed,shipped,delivered,cancelled,refunded',
        ]);

        $oldStatus = $order->status;
        $order->status = $request->status;

        // Update timestamps based on status
        if ($request->status === 'shipped' && !$order->shipped_at) {
            $order->shipped_at = now();
        }

        if ($request->status === 'delivered' && !$order->delivered_at) {
            $order->delivered_at = now();
        }

        $order->save();

        // Create status history
        OrderStatusHistory::create([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'status' => $request->status,
            'comment' => "Status changed from {$oldStatus} to {$request->status}",
            'notify_customer' => false,
        ]);

        return redirect()->route('orders.show', $order)->with('success', 'Order status updated successfully');
    }

    /**
     * Update the order payment status.
     */
    public function updatePaymentStatus(Request $request, Order $order)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid,failed,refunded',
        ]);

        $order->payment_status = $request->payment_status;

        if ($request->payment_status === 'paid' && !$order->paid_at) {
            $order->paid_at = now();
        }

        $order->save();

        // Create status history
        OrderStatusHistory::create([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'status' => $order->status,
            'comment' => "Payment status updated to {$request->payment_status}",
            'notify_customer' => false,
        ]);

        return redirect()->route('orders.show', $order)->with('success', 'Payment status updated successfully');
    }

    /**
     * Add tracking number to order.
     */
    public function addTrackingNumber(Request $request, Order $order)
    {
        $request->validate([
            'tracking_number' => 'required|string|max:255',
        ]);

        $order->tracking_number = $request->tracking_number;
        $order->save();

        // Create status history
        OrderStatusHistory::create([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'status' => $order->status,
            'comment' => "Tracking number added: {$request->tracking_number}",
            'notify_customer' => true,
        ]);

        return redirect()->route('orders.show', $order)->with('success', 'Tracking number added successfully');
    }

    /**
     * Get status colors
     */
    private function getStatusColors(): array
    {
        return [
            'pending' => 'warning',
            'processing' => 'info',
            'confirmed' => 'primary',
            'shipped' => 'secondary',
            'delivered' => 'success',
            'cancelled' => 'danger',
            'refunded' => 'dark',
        ];
    }

    /**
     * Get payment status colors
     */
    private function getPaymentStatusColors(): array
    {
        return [
            'pending' => 'warning',
            'paid' => 'success',
            'failed' => 'danger',
            'refunded' => 'dark',
        ];
    }

    /**
     * Export orders to CSV
     */
    public function export(Request $request)
    {
        $query = Order::with(['customer'])
            ->orderBy('created_at', 'desc');

        // Apply same filters as index
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('payment_status') && $request->payment_status !== 'all') {
            $query->where('payment_status', $request->payment_status);
        }

        $orders = $query->get();

        $filename = 'orders_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($orders) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, [
                'Order Number',
                'Customer Name',
                'Customer Email',
                'Status',
                'Payment Status',
                'Payment Method',
                'Subtotal',
                'Tax',
                'Shipping',
                'Discount',
                'Total',
                'Order Date',
                'Paid Date',
                'Shipped Date',
                'Delivered Date'
            ]);

            // Add data rows
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_number,
                    $order->customer ? $order->customer->first_name . ' ' . $order->customer->last_name : $order->shipping_full_name,
                    $order->customer ? $order->customer->email : $order->shipping_email,
                    $order->status,
                    $order->payment_status,
                    $order->payment_method,
                    $order->subtotal,
                    $order->tax,
                    $order->shipping_cost,
                    $order->discount,
                    $order->total,
                    $order->created_at->format('Y-m-d H:i:s'),
                    $order->paid_at ? $order->paid_at->format('Y-m-d H:i:s') : '',
                    $order->shipped_at ? $order->shipped_at->format('Y-m-d H:i:s') : '',
                    $order->delivered_at ? $order->delivered_at->format('Y-m-d H:i:s') : '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
