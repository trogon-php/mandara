<?php

namespace App\Http\Requests\Notifications;

use App\Http\Requests\BaseRequest;

class StoreNotificationRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:1000',
            'description' => 'nullable|string|max:1000',
            'course_id' => 'nullable|exists:courses,id',
            'category_id' => 'nullable|exists:categories,id',
            'premium' => 'nullable|boolean',
            'free' => 'nullable|boolean',
            'action_link' => 'nullable|string|max:155',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];
    }
}



