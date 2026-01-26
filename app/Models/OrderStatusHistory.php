<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'status',
        'comment',
        'notify_customer',
    ];

    protected $casts = [
        'notify_customer' => 'boolean',
    ];

    /**
     * Get the order that owns the status history
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the user that created the status history
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
