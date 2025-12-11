<?php

namespace App\Http\Resources\FeedCategories;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class FeedCategoryResource extends BaseResource
{
    protected function resourceFields(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'status' => $this->status,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
