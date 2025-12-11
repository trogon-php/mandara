<?php

namespace App\Http\Requests\Blogs;

use App\Http\Requests\BaseRequest;

class StoreBlogRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blogs,slug',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'short_description' => 'nullable|string|max:500',
            'content' => 'required|string',
            'status' => 'required|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Blog title is required',
            'title.max' => 'Blog title cannot exceed 255 characters',
            'slug.unique' => 'This slug is already in use',
            'slug.max' => 'Slug cannot exceed 255 characters',
            'image.image' => 'File must be an image',
            'image.mimes' => 'Image must be jpeg, png, jpg, gif, or webp',
            'image.max' => 'Image size cannot exceed 2MB',
            'short_description.max' => 'Short description cannot exceed 500 characters',
            'content.required' => 'Blog content is required',
            'status.required' => 'Status is required',
            'status.in' => 'Status must be either 0 (Inactive) or 1 (Active)',
        ];
    }
}