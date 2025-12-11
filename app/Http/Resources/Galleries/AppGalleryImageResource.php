<?php

namespace App\Http\Resources\Galleries;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class AppGalleryImageResource extends BaseResource
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
            'image_url' => $this->image_url,
            'album_title' => $this->whenLoaded('album', function () {
                return $this->album->title ?? null;
            }),
            'created_at' => $this->created_at->diffForHumans(),
        ];
    }
}