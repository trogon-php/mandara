<?php

namespace App\Http\Requests\MealPackages;

use App\Http\Requests\BaseRequest;

class UpdateMealPackageRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'short_description' => 'sometimes|nullable|string',
            'thumbnail' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'content' => 'sometimes|nullable|string',
            'labels' => 'sometimes|nullable|string|max:255',
            'is_veg' => 'sometimes|nullable|boolean',
            'status' => 'sometimes|nullable|boolean',
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