<?php

namespace App\Http\Resources\Estore;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class AppEstoreCartResource extends BaseResource
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
            'product' => $this->whenLoaded('product', function () {
                return new AppEstoreProductResource($this->product);
            }),
            'subtotal' => number_format($this->product->price * $this->quantity, 2, '.', ''),
        ];
    }
}