<?php

namespace App\Http\Requests\PackageFeatures;

use App\Http\Requests\BaseRequest;

class UpdatePackageFeatureRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'package_id' => 'required|exists:packages,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'sort_order' => 'nullable|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'package_id.required' => 'Package selection is required.',
            'package_id.exists' => 'Selected package does not exist.',
            'title.required' => 'Feature title is required.',
            'title.string' => 'Feature title must be a string.',
            'title.max' => 'Feature title must not exceed 255 characters.',
            'description.string' => 'Description must be a string.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be active or inactive.',
            'sort_order.integer' => 'Sort order must be an integer.',
            'sort_order.min' => 'Sort order must be at least 0.',
        ];
    }
}

