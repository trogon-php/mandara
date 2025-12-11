<?php

namespace App\Http\Resources\Notifications;

use App\Http\Resources\BasePaginatedResourceCollection;
use Illuminate\Http\Request;

class AppNotificationCollection extends BasePaginatedResourceCollection
{
    public function toArray($request): array
    {
        return [
            'status'  => true,
            'message' => $this->message,
            'data'    => $this->collection->map(
                fn ($item) => (new AppNotificationResource($item))->toArray($request)
            ),
            'meta'    => [
                'total'        => $this->resource->total(),
                'per_page'     => $this->resource->perPage(),
                'current_page' => $this->resource->currentPage(),
                'last_page'    => $this->resource->lastPage(),
            ],
        ];
    }
}
