<?php

namespace App\Http\Resources\Reels;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class AppReelResource extends BaseResource
{
    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->includeId = true;
    }

    protected function resourceFields(Request $request): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'thumbnail' => !empty($this->thumbnail) ? $this->thumbnail_url : null,
            'video_url' => !empty($this->video) ? $this->video_url : null,
            'created_at' => $this->created_at->diffForHumans(),
        ];
    }
}