<?php

namespace App\Models;

class EstoreOrder extends BaseModel
{
    protected $fillable = [
        'user_id',
        'order_status',
        'order_number',
        'payment_status',
        'payment_method',
        'payment_order_id',
        'payment_id',
        'total_amount',
        'discount_amount',
        'payable_amount',
        'notes',
        'delivered_at',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'payable_amount' => 'decimal:2',
        'delivered_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(EstoreOrderItem::class, 'order_id');
    }

    public function scopePending($query)
    {
        return $query->where('order_status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('order_status', 'confirmed');
    }

    public function scopeDelivered($query)
    {
        return $query->where('order_status', 'delivered');
    }

    public function scopeCancelled($query)
    {
        return $query->where('order_status', 'cancelled');
    }
}
