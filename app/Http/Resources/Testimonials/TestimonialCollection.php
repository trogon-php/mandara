<?php

namespace App\Http\Resources\Testimonials;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TestimonialCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(
                fn ($item) => (new TestimonialResource($item))->toArray($request)
            ),
            'meta' => [
                'count' => $this->collection->count(),
            ],
        ];
    }
}