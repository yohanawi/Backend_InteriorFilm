<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_id',
        'status',
        'payment_status',
        'payment_method',
        'payment_transaction_id',
        'payment_completed_at',
        'payment_error_message',
        'payment_metadata',
        'ngenius_reference',
        'ngenius_payment_url',
        'ngenius_currency',
        'ngenius_amount_minor',
        'ngenius_last_payment_state',
        'card_scheme',
        'card_last4',
        'transaction_id',
        'shipping_first_name',
        'shipping_last_name',
        'shipping_email',
        'shipping_phone',
        'shipping_address',
        'shipping_city',
        'shipping_state',
        'shipping_country',
        'shipping_postal_code',
        'billing_first_name',
        'billing_last_name',
        'billing_email',
        'billing_phone',
        'billing_address',
        'billing_city',
        'billing_state',
        'billing_country',
        'billing_postal_code',
        'subtotal',
        'tax',
        'shipping_cost',
        'discount',
        'total',
        'total_amount',
        'notes',
        'coupon_code',
        'tracking_number',
        'paid_at',
        'shipped_at',
        'delivered_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'payment_completed_at' => 'datetime',
        'payment_metadata' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (!$order->order_number) {
                $order->order_number = self::generateOrderNumber();
            }

            // Sync total_amount with total
            if ($order->total && !$order->total_amount) {
                $order->total_amount = $order->total;
            }
        });

        static::updating(function ($order) {
            // Keep total_amount in sync with total
            if ($order->isDirty('total')) {
                $order->total_amount = $order->total;
            }
        });
    }

    /**
     * Generate a unique order number
     */
    public static function generateOrderNumber(): string
    {
        do {
            $number = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        } while (self::where('order_number', $number)->exists());

        return $number;
    }

    /**
     * Get the customer that owns the order
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the items for the order
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the status histories for the order
     */
    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    /**
     * Get the full shipping name
     */
    public function getShippingFullNameAttribute(): string
    {
        return trim($this->shipping_first_name . ' ' . $this->shipping_last_name);
    }

    /**
     * Get the full billing name
     */
    public function getBillingFullNameAttribute(): string
    {
        return trim(($this->billing_first_name ?? '') . ' ' . ($this->billing_last_name ?? ''));
    }

    /**
     * Check if order can be cancelled
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'processing', 'confirmed']);
    }

    /**
     * Check if order is paid
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Scope to filter by status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get recent orders
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope to filter by payment status
     */
    public function scopePaymentStatus($query, $status)
    {
        return $query->where('payment_status', $status);
    }

    /**
     * Scope with optimized relationships for listing
     */
    public function scopeWithBasicRelations($query)
    {
        return $query->with(['customer:id,first_name,last_name,email', 'items']);
    }

    /**
     * Scope with full relationships for details
     */
    public function scopeWithFullRelations($query)
    {
        return $query->with([
            'customer:id,first_name,last_name,email,phone',
            'items.product:id,name,slug,sku',
            'statusHistories.user:id,name'
        ]);
    }

    /**
     * Calculate order total from items
     */
    public function calculateTotal(): void
    {
        $this->subtotal = $this->items->sum('subtotal');
        $this->tax = $this->items->sum('tax');
        $this->total = $this->subtotal + $this->tax + $this->shipping_cost - $this->discount;
        $this->total_amount = $this->total;
    }
}
