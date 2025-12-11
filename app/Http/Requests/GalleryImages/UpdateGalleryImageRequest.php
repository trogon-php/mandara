<?php

namespace App\Http\Requests\GalleryImages;

use App\Http\Requests\BaseRequest;

class UpdateGalleryImageRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'album_id' => 'required|integer|exists:gallery_albums,id',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status' => 'required|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'album_id.required' => 'Album selection is required',
            'album_id.integer' => 'Album ID must be a valid number',
            'album_id.exists' => 'Selected album does not exist',
            'title.max' => 'Image title cannot exceed 255 characters',
            'description.string' => 'Description must be a valid text',
            'image.image' => 'File must be an image',
            'image.mimes' => 'Image must be jpeg, png, jpg, gif, or webp',
            'image.max' => 'Image size cannot exceed 2MB',
            'status.required' => 'Status is required',
            'status.in' => 'Status must be either 0 (Inactive) or 1 (Active)',
        ];
    }
}
