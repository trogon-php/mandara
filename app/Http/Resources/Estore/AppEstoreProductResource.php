<?php

namespace App\Http\Resources\Estore;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class AppEstoreProductResource extends BaseResource
{
    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->includeId = true;
    }

    protected function resourceFields(Request $request): array
    {
        $images = $this->images_url;

        if($request->has('cartList')){
            return [
                'title' => $this->title,
                'image' => $images[0] ?? null,
            ];
        }
        if($request->has('list')){

            return [
                'title' => $this->title,
                'price' => round($this->price, 2),
                'mrp' => $this->mrp ? round($this->mrp, 2) : null,
                'image' => $images[0] ?? null,
            ];
        }
        return [
            'title' => $this->title,
            // 'short_description' => $this->short_description,
            'description' => $this->description,
            'price' => round($this->price, 2),
            'mrp' => $this->mrp ? round($this->mrp, 2) : null,
            'discount_percentage' => $this->discount_percentage,
            // 'stock' => $this->stock,
            'is_featured' => (bool)$this->is_featured,
            'images' => $images ?? [],
            // 'category' => $this->whenLoaded('category', function () {
            //     return [
            //         'id' => $this->category->id,
            //         'title' => $this->category->title,
            //     ];
            // }),
        ];
    }
}