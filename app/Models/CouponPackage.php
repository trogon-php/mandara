<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CouponPackage extends BaseModel
{
    protected $table = 'coupon_packages';

    protected $fillable = [
        'coupon_id',
        'package_id',
    ];

    /**
     * Get the coupon that owns this package association
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Get the package that is associated with this coupon
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }
}


