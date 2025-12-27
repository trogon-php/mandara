<?php

namespace App\Models;

class Payment extends BaseModel
{
    protected $fillable = [
        'order_id',
        'user_id',
        'package_id',
        'amount_total',
        'amount_paid',
        'payment_status',
        'transaction_id',
        'remarks',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'amount_total' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'payment_status' => 'string',
    ];

    /**
     * Get the order that owns the payment
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the user that owns the payment
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the package associated with the payment
     */
    public function package()
    {
        return $this->belongsTo(CottagePackage::class);
    }
    /**
     * Scope to filter by payment status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('payment_status', $status);
    }

    /**
     * Scope to filter by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter by package
     */
    public function scopeByPackage($query, $packageId)
    {
        return $query->where('package_id', $packageId);
    }

    /**
     * Scope to filter by order
     */
    public function scopeByOrder($query, $orderId)
    {
        return $query->where('order_id', $orderId);
    }
}
