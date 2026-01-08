<?php

namespace App\Http\Resources\Estore;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class DeliveryOrderResource extends BaseResource
{
    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->includeId = false;
    }

    protected function resourceFields(Request $request): array
    {
        $order = $this->order ?? null;
        $user = $order->user ?? null;

        return [
            // Assignment details
            'assignment_id' => $this->id,
            'status' => $this->status,
            // 'assigned_at' => $this->assigned_at?->format('Y-m-d H:i:s'),
            // 'started_at' => $this->started_at?->format('Y-m-d H:i:s'),
            'delivered_at' => "Delivered at ". $this->delivered_at?->format('h:i A'),
            'delivery_room' => $this->delivery_room,
            'delivery_remarks' => $this->delivery_remarks,
            
            // Order details
            'order' => $this->when($order, function () use ($order) {
                return [
                    'id' => $order->id,
                    // 'order_number' => $order->order_number,
                    // 'order_status' => $order->order_status,
                    'payment_status' => $order->payment_status,
                    'payment_method' => $order->payment_method,
                    // 'total_amount' => number_format($order->total_amount, 2, '.', ''),
                    // 'discount_amount' => number_format($order->discount_amount, 2, '.', ''),
                    'payable_amount' => number_format($order->payable_amount, 2, '.', ''),
                    'ordered_at' => "Ordered at ". $order->created_at->format('h:i A'),
                    // 'created_at_formatted' => $order->created_at->format('M d, Y h:i A'),
                ];
            }),
            
            // Customer details
            'customer' => $this->when($user, function () use ($user) {
                return [
                    // 'id' => $user->id,
                    'name' => $user->name,
                    // 'email' => $user->email,
                    // 'phone' => $user->phone,
                    // 'country_code' => $user->country_code,
                ];
            }),
            
            // Order items
            'items' => $this->when($order && $order->relationLoaded('items'), function () use ($order) {
                return $order->items->map(function ($item) {
                    return [
                        'item' => $item->product?->title . "  X $item->quantity",
                        // 'id' => $item->id,
                        // 'product' => [
                        //     'id' => $item->product?->id ?? null,
                        //     'title' => $item->product?->title ?? 'Product Not Available',
                        //     'image' => $item->product?->images_url ? ($item->product->images_url[0] ?? null) : null,
                        // ],
                        // 'unit_price' => number_format($item->unit_price, 2, '.', ''),
                        'quantity' => $item->quantity,
                        // 'total_amount' => number_format($item->total_amount, 2, '.', ''),
                    ];
                });
            }),
        ];
    }
}