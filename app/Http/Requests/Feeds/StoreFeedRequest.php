<?php

namespace App\Http\Requests\Feeds;

use App\Http\Requests\BaseRequest;

class StoreFeedRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:200',
            'content' => 'nullable|string',
            'feed_category_id' => 'nullable|exists:feed_categories,id',
            'course_id' => 'nullable|integer',
            'feed_image' => 'nullable|array',
            'feed_image.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,bmp,svg|max:2048',
            'feed_video' => 'nullable|file|mimes:mp4,avi,mov,wmv|max:10240',
            'status' => 'required|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Title is required.',
            'title.max' => 'Title must not exceed 200 characters.',
            'feed_category_id.exists' => 'Selected category does not exist.',
            'course_id.exists' => 'Selected course does not exist.',
            'feed_image.*.image' => 'All feed images must be valid images.',
            'feed_video.file' => 'Feed video must be a valid file.',
            'feed_video.mimes' => 'Feed video must be a video file (mp4, avi, mov, wmv).',
            'feed_video.max' => 'Feed video must not exceed 10MB.',
            'status.required' => 'Status is required.',
        ];
    }
}
