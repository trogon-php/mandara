<?php

namespace App\Models;


class CottagePackage extends BaseModel
{
    protected $table = 'cottage_packages';

    protected $casts = [
        'price' => 'decimal:2',
        'offer_price' => 'decimal:2',
        'duration_days' => 'integer',
        'sort_order' => 'integer',
    ];

    protected $fillable = [
        'title',
        'description',
        'cottage_category_id',
        'price',
        'offer_price',
        'duration_days',
        'status',
    ];

    public function cottageCategory()
    {
        return $this->belongsTo(CottageCategory::class);
    }

    public function hasOffer(): bool
    {
        return !is_null($this->offer_price);
    }

}
