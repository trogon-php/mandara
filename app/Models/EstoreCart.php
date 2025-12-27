<?php

namespace App\Models;

class EstoreCart extends BaseModel
{
    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
    ];

    protected $casts = [
        'user_id' => 'string',
        'product_id' => 'integer',
        'quantity' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(EstoreProduct::class, 'product_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
