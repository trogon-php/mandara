<?php

namespace App\Http\Resources\Galleries;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class AppGalleryAlbumWithImagesResource extends BaseResource
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
            'thumbnail_url' => $this->thumbnail_url,
            'images' => AppGalleryImageResource::collection($this->whenLoaded('images')),
            'active_images' => AppGalleryImageResource::collection($this->whenLoaded('activeImages')),
            'images_count' => $this->whenLoaded('images', function () {
                return $this->images->count();
            }),
            'active_images_count' => $this->whenLoaded('activeImages', function () {
                return $this->activeImages->count();
            }),
            'created_at' => $this->created_at->diffForHumans(),
        ];
    }
}