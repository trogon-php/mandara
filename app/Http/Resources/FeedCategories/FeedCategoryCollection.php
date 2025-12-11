<?php

namespace App\Http\Resources\FeedCategories;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class FeedCategoryCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(
                fn ($item) => (new FeedCategoryResource($item))->toArray($request)
            ),
            'meta' => [
                'count' => $this->collection->count(),
            ],
        ];
    }
}
