<?php

namespace App\Http\Requests\MealPackages;

use App\Http\Requests\BaseRequest;

class StoreMealPackageRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'short_description' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'content' => 'nullable|string',
            'labels' => 'nullable|string|max:255',
            'is_veg' => 'nullable|boolean',
            'status' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Title is required',
            'title.max' => 'Title cannot exceed 255 characters',
            'thumbnail.image' => 'Thumbnail must be an image',
            'thumbnail.mimes' => 'Thumbnail must be a jpeg, png, jpg, gif, or svg file',
            'thumbnail.max' => 'Thumbnail must not exceed 2MB',
            'labels.max' => 'Labels cannot exceed 255 characters',
        ];
    }
}