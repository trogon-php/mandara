<?php

namespace App\Http\Resources\Food;

use Illuminate\Http\Request;
use App\Http\Resources\BaseResource;

class AppFoodItemResource extends BaseResource
{
    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->includeId = true;
    }

    protected function resourceFields(Request $request): array
    {
        return [
            'title' => $this->title,
            'price' => $this->price,
            'stock' => $this->stock,
            'image' => $this->image_url,
            'cart_quantity' => $this->getCartQuantity(),
        ];
    }
    
    protected function getCartQuantity(): int
    {
        if (!$this->relationLoaded('cartItems')) {
            return 0;
        }

        $cartItem = $this->cartItems->first();
        return $cartItem ? $cartItem->quantity : 0;
    }
}
