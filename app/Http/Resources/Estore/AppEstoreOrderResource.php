<?php

namespace App\Http\Resources\Estore;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class AppEstoreOrderResource extends BaseResource
{
    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->includeId = true;
    }

    protected function resourceFields(Request $request): array
    {
        return [
            'order_number' => $this->order_number,
            'order_status' => $this->order_status,
            'payment_status' => $this->payment_status,
            'payment_method' => $this->payment_method,
            'total_amount' => number_format($this->total_amount, 2, '.', ''),
            'discount_amount' => number_format($this->discount_amount, 2, '.', ''),
            'payable_amount' => number_format($this->payable_amount, 2, '.', ''),
            'notes' => $this->notes,
            'delivered_at' => $this->delivered_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at->format('M d, Y'),
            'items' => $this->whenLoaded('items', function () {
                return $this->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product' => [
                            'id' => $item->product->id,
                            'title' => $item->product->title,
                            'image' => $item->product->images_url ? $item->product->images_url[0] : '',
                        ],
                        'unit_price' => number_format($item->unit_price, 2, '.', ''),
                        'quantity' => $item->quantity,
                        'total_amount' => number_format($item->total_amount, 2, '.', ''),
                    ];
                });
            }),
        ];
    }
}