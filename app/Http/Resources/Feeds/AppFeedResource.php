<?php

namespace App\Http\Resources\Feeds;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class AppFeedResource extends BaseResource
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
            'content' => $this->content,
            'is_liked' => $this->is_liked ?? false,
            'likes_count' => $this->likes_count ?? 0,
            'media' => $this->getMediaArray(),
            'created_at' => $this->created_at->diffForHumans(),
        ];
    }

    /**
     * Combine feed images and videos into a single media array
     */
    private function getMediaArray(): array
    {
        $media = [];
        
        // Add images if they exist
        if (!empty($this->feed_image) && is_array($this->feed_image_url)) {
            foreach ($this->feed_image_url as $image) {
                $media[] = [
                    'type' => 'image',
                    'image' => $image
                ];
            }
        }
        
        // Add video if it exists
        if (!empty($this->feed_video)) {
            $media[] = [
                'type' => 'video',
                'image' => null,
                'video' => $this->feed_video_url
            ];
        }
        
        return $media;
    }
}
