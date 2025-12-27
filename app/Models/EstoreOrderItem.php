<?php

namespace App\Models;

class EstoreOrderItem extends BaseModel
{
    protected $fillable = [
        'order_id',
        'product_id',
        'unit_price',
        'quantity',
        'total_amount',
    ];

    protected $casts = [
        'order_id' => 'string',
        'product_id' => 'integer',
        'unit_price' => 'decimal:2',
        'quantity' => 'integer',
        'total_amount' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(EstoreOrder::class, 'order_id', 'payment_order_id');
    }

    public function product()
    {
        return $this->belongsTo(EstoreProduct::class, 'product_id');
    }
}
