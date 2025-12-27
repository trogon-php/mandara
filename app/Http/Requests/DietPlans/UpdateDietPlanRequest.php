<?php

namespace App\Http\Requests\DietPlans;

use App\Http\Requests\BaseRequest;

class UpdateDietPlanRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|nullable|string|max:255',
            'month' => 'sometimes|required|integer|min:1',
            'image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'short_description' => 'sometimes|nullable|string|max:255',
            'content' => 'sometimes|required|string',
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