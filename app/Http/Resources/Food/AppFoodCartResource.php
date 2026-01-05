<?php

namespace App\Http\Resources\Food;

use Illuminate\Http\Request;
use App\Http\Resources\BaseResource;

class AppFoodCartResource extends BaseResource
{
    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->includeId = true;
    }

    protected function resourceFields(Request $request): array
    {
        return [
            'quantity' => $this->quantity,
            'item' => $this->whenLoaded('item', function () {
                return new AppFoodItemResource($this->item);
            }),
            'subtotal' => number_format($this->item->price * $this->quantity, 2, '.', ''),
        ];
    }
}