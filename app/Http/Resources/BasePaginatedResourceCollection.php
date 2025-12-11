<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class BasePaginatedResourceCollection extends ResourceCollection
{
    protected string $message;

    public function __construct($resource, string $message = 'Fetched successfully')
    {
        parent::__construct($resource);
        $this->message = $message;
    }

    /**
     * Format the response for paginated collections
     */
    public function toArray($request): array
    {
        return [
            'status'  => true,
            'message' => $this->message,
            'data'    => $this->collection, // children should wrap with Resource
            'meta'    => [
                'total'        => $this->resource->total(),
                'per_page'     => $this->resource->perPage(),
                'current_page' => $this->resource->currentPage(),
                'last_page'    => $this->resource->lastPage(),
            ],
        ];
    }
}
