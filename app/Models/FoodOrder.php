<?php

namespace App\Models;

class FoodOrder extends BaseModel
{
    protected $fillable = [
        'user_id',
        'order_number',
        'order_date',
        'order_status',
        'payment_status',
        'payment_method',
        'payment_order_id',
        'payment_id',
        'total_amount',
        'discount_amount',
        'taxes_and_fees',
        'payable_amount',
        'delivery_room',
        'notes',
        'delivered_at',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'order_date' => 'date',
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'taxes_and_fees' => 'decimal:2',
        'payable_amount' => 'decimal:2',
        'delivered_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(FoodOrderItem::class, 'order_id');
    }

    public function scopePending($query)
    {
        return $query->where('order_status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('order_status', 'confirmed');
    }

    public function scopeProcessing($query)
    {
        return $query->where('order_status', 'processing');
    }

    public function scopeDelivered($query)
    {
        return $query->where('order_status', 'delivered');
    }

    public function scopeCancelled($query)
    {
        return $query->where('order_status', 'cancelled');
    }

    public function scopeByDate($query, $date)
    {
        return $query->where('order_date', $date);
    }
}