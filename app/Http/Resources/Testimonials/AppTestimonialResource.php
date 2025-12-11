<?php

namespace App\Http\Resources\Testimonials;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class AppTestimonialResource extends BaseResource
{
    public function __construct($resource)
    {
        parent::__construct($resource);
    }

    protected function resourceFields(Request $request): array
    {
        return [
            'content' => $this->content,
            'user_name' => $this->user_name,
            'designation' => $this->designation,
            'rating' => $this->rating,
            'profile_image' => $this->profile_image_url
        ];
    }
}
