<?php

namespace App\Http\Requests\DietPlans;

use App\Http\Requests\BaseRequest;

class StoreDietPlanRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'month' => 'required|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'short_description' => 'nullable|string|max:255',
            'content' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Title is required',
            'title.max' => 'Title cannot exceed 255 characters',
            'slug.max' => 'Slug cannot exceed 255 characters',
            'month.required' => 'Month is required',
            'month.integer' => 'Month must be an integer',
            'month.min' => 'Month must be at least 1',
        ];
    }
}