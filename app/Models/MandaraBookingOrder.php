<?php

namespace App\Models;

class MandaraBookingOrder extends BaseModel
{
    protected $fillable = [
        'booking_id',
        'payment_status',
        'payment_method',
        'payment_order_id',
        'payment_id',
        'type',
        'total_amount',
        'discount_amount',
        'payable_amount',
        'paid_amount',
        'notes',
        'remarks',
    ];

    protected $casts = [
        'booking_id' => 'integer',
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'payable_amount' => 'decimal:2',
    ];

    public function booking()
    {
        return $this->belongsTo(MandaraBooking::class, 'booking_id');
    }
}
