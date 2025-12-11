<?php

namespace App\Http\Resources\Packages;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class AppPackageCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return $this->collection->map(
            fn ($item) => (new AppPackageResource($item))->toArray($request)
        )->toArray();
    }
}
