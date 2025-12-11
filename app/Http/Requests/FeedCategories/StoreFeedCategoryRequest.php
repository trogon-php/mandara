<?php

namespace App\Http\Requests\FeedCategories;

use App\Http\Requests\BaseRequest;

class StoreFeedCategoryRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:100',
            'status' => 'required|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Title is required.',
            'title.max' => 'Title must not exceed 100 characters.',
            'status.required' => 'Status is required.',
        ];
    }
}
