<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MandaraBooking extends BaseModel
{
    protected $fillable = [
        'booking_number',
        'user_id',
        'cottage_package_id',
        'date_from',
        'date_to',
        'is_delivered',
        'delivery_date',
        'remarks',
        'blood_group',
        'is_veg',
        'diet_remarks',
        'address',
        'pickup_address',
        'have_caretaker',
        'caretaker_name',
        'husband_name',
        'caretaker_age',
        'have_siblings',
        'booking_amount',
        'total_amount',
        'payable_amount',
        'offer_amount',
        'booking_payment_status',
        'approval_status',
        'additional_note',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'cottage_package_id' => 'integer',
        'booking_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'payable_amount' => 'decimal:2',
        'have_caretaker' => 'integer',
        'have_siblings' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cottagePackage(): BelongsTo
    {
        return $this->belongsTo(CottagePackage::class);
    }
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($booking) {
            if (empty($booking->booking_number)) {
                $booking->booking_number = 'MNDR-' . date('Ymd') . '-' . str_pad(
                    static::whereDate('created_at', today())->count() + 1,
                    3,
                    '0',
                    STR_PAD_LEFT
                );
            }
        });
    }
}
