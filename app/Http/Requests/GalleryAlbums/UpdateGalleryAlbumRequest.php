<?php

namespace App\Http\Requests\GalleryAlbums;

use App\Http\Requests\BaseRequest;

class UpdateGalleryAlbumRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status' => 'required|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Album title is required',
            'title.max' => 'Album title cannot exceed 255 characters',
            'description.string' => 'Description must be a valid text',
            'thumbnail.image' => 'File must be an image',
            'thumbnail.mimes' => 'Image must be jpeg, png, jpg, gif, or webp',
            'thumbnail.max' => 'Image size cannot exceed 2MB',
            'status.required' => 'Status is required',
            'status.in' => 'Status must be either 0 (Inactive) or 1 (Active)',
        ];
    }
}
