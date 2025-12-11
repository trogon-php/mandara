<?php

namespace App\Http\Requests\ReelCategories;

use App\Http\Requests\BaseRequest;
use App\Models\ReelCategory;

class StoreReelCategoryRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'parent_id' => 'nullable|integer|exists:reel_categories,id',
            'status' => 'required|in:0,1',
            'sort_order' => 'nullable|integer|min:0',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Reel category title is required',
            'title.max' => 'Reel category title cannot exceed 255 characters',
            
            'parent_id.integer' => 'Parent category must be a valid number',
            'parent_id.exists' => 'Selected parent category does not exist',
            
            'status.required' => 'Status is required',
            'status.in' => 'Status must be either 0 (Inactive) or 1 (Active)',
            
            'sort_order.integer' => 'Sort order must be a valid number',
            'sort_order.min' => 'Sort order must be 0 or greater',
            
            'thumbnail.image' => 'File must be an image',
            'thumbnail.mimes' => 'Image must be jpeg, png, jpg, gif, or webp',
            'thumbnail.max' => 'Image size cannot exceed 2MB',
        ];
    }
}

