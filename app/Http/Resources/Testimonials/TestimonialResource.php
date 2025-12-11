<?php

namespace App\Http\Resources\Testimonials;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class TestimonialResource extends BaseResource
{
    protected function resourceFields(Request $request): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'user_name' => $this->user_name,
            'designation' => $this->designation,
            'rating' => $this->rating,
            'status' => $this->status,
            'profile_image' => $this->profile_image_url, // Using HasFileUrls trait
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}