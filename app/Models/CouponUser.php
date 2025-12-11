<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CouponUser extends BaseModel
{
    protected $table = 'coupon_users';

    protected $fillable = [
        'coupon_id',
        'user_id',
    ];

    /**
     * Get the coupon that owns this user association
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Get the user that is associated with this coupon
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}


