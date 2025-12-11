<?php

namespace App\Http\Resources\Packages;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class AppPackageResource extends BaseResource
{
    protected $isFirst = false;

    public function __construct($resource, $isFirst = false)
    {
        parent::__construct($resource);
        $this->includeId = true;
        $this->isFirst = $isFirst;
    }

    protected function resourceFields(Request $request): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price,
            'offer_price' => $this->offer_price,
            'effective_price' => $this->effective_price,
            'has_offer' => $this->hasOffer(),
            'discount_percentage' => $this->discount_percentage,
            'is_recommended' => $this->isFirst,
            // 'duration_days' => $this->duration_days,
            // 'expire_date' => $this->expire_date?->format('Y-m-d'),
            // 'is_expired' => $this->isExpired(),
            'items' => $this->whenLoaded('items', function () {
                return $this->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'item_type' => $item->item_type,
                        'item_id' => $item->item_id,
                        'item_title' => $item->item_title,
                    ];
                });
            }),
            'features' => $this->whenLoaded('features', function () {
                return $this->features->map(function ($feature) {
                    return [
                        'id' => $feature->id,
                        'title' => $feature->title,
                        'description' => $feature->description,
                    ];
                });
            }),
        ];
    }
}
