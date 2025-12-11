<?php

namespace App\Http\Resources\Feeds;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class FeedResource extends BaseResource
{
    protected function resourceFields(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'feed_category_id' => $this->feed_category_id,
            'course_id' => $this->course_id,
            'feed_type' => $this->feed_type,
            'feed_image' => $this->feed_image,
            'feed_video' => $this->feed_video,
            'status' => $this->status,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'feed_category' => $this->whenLoaded('feedCategory'),
            'course' => $this->whenLoaded('course'),
        ];
    }
}