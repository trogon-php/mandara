<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends BaseModel
{
    protected $casts = [
        'user_id' => 'integer',
        'package_id' => 'integer',
        'coupon_id' => 'integer',
        'amount_total' => 'decimal:2',
        'amount_offer' => 'decimal:2',
        'amount_final' => 'decimal:2',
        'status' => 'string',
    ];

    protected $fillable = [
        'order_number',
        'user_id',
        'package_id',
        'coupon_id',
        'coupon_code',
        'amount_total',
        'amount_offer',
        'amount_final',
        'status',
    ];

    /**
     * Get the user who placed the order
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the package for this order
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * Get the coupon used in this order
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Get the coupon usages for this order
     */
    public function couponUsages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    /**
     * Get the user payments for this order
     */
    public function userPayments(): HasMany
    {
        return $this->hasMany(\App\Models\UserPayment::class);
    }

    /**
     * Scope for pending orders
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for paid orders
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope for cancelled orders
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Check if order is paid
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Check if order is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if order is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Get the discount amount
     */
    public function getDiscountAmountAttribute(): float
    {
        return $this->amount_offer ?? 0;
    }

    /**
     * Get the savings amount
     */
    public function getSavingsAmountAttribute(): float
    {
        return $this->amount_total - $this->amount_final;
    }
}
