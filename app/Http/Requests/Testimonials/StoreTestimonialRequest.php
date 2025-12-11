<?php

namespace App\Http\Requests\Testimonials;

use App\Http\Requests\BaseRequest;

class StoreTestimonialRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'content' => 'required|string|max:1000',
            'user_name' => 'required|string|max:255',
            'designation' => 'nullable|string|max:50',
            'rating' => 'nullable|integer|min:1|max:5',
            'status' => 'required|in:0,1',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'Content is required.',
            'user_name.required' => 'User name is required.',
            'status.required' => 'Status is required.',
            'rating.integer' => 'Rating must be a number.',
            'rating.min' => 'Rating must be at least 1.',
            'rating.max' => 'Rating must be at most 5.',
            'profile_image.image' => 'File must be an image.',
        ];
    }
}