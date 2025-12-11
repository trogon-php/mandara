<?php

namespace App\Http\Requests\Reels;

use App\Http\Requests\BaseRequest;
use App\Models\ReelCategory;

class StoreReelRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video' => 'required|file|mimes:mp4,avi,mov,wmv,flv,webm|max:30720', // 30MB max
            'reel_category_id' => 'nullable|integer|exists:reel_categories,id',
            'status' => 'required|in:0,1',
            'premium' => 'required|in:0,1',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Reel title is required',
            'title.max' => 'Reel title cannot exceed 255 characters',
            
            'description.string' => 'Description must be text',
            
            'video.required' => 'Video file is required',
            'video.file' => 'Video must be a valid file',
            'video.mimes' => 'Video must be mp4, avi, mov, wmv, flv, or webm format',
            'video.max' => 'Video size cannot exceed 30MB',
            
            'reel_category_id.integer' => 'Reel category must be a valid number',
            'reel_category_id.exists' => 'Selected reel category does not exist',
            
            'status.required' => 'Status is required',
            'status.in' => 'Status must be either 0 (Inactive) or 1 (Active)',
            
            'premium.required' => 'Content type is required',
            'premium.in' => 'Content type must be either 0 (Free) or 1 (Premium)',
            
            'thumbnail.required' => 'Thumbnail is required',
            'thumbnail.image' => 'Thumbnail must be an image',
            'thumbnail.mimes' => 'Thumbnail must be jpeg, png, jpg, gif, or webp',
            'thumbnail.max' => 'Thumbnail size cannot exceed 2MB',
        ];
    }
}
